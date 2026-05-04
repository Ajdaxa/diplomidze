<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\DaDataService;
use App\Services\YooKassaService;
use App\Support\PromocodePricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly DaDataService $daDataService,
        private readonly YooKassaService $yooKassaService,
    ) {
    }

    private function cartLines(): array
    {
        $raw = session('cart', []);
        $lines = [];
        foreach ($raw as $key => $qty) {
            if (! is_string($key) || ! str_contains($key, '|')) {
                continue;
            }
            [$id, $size] = explode('|', $key, 2);
            $lines[] = [
                'product_id' => (int) $id,
                'size' => $size,
                'quantity' => max(1, (int) $qty),
            ];
        }

        return $lines;
    }

    private function buildCartItems(): \Illuminate\Support\Collection
    {
        $lines = $this->cartLines();
        $products = Product::query()
            ->whereIn('id', collect($lines)->pluck('product_id')->unique())
            ->get()
            ->keyBy('id');

        return collect($lines)->map(function (array $line) use ($products) {
            $product = $products->get($line['product_id']);
            if (! $product) {
                return null;
            }

            return [
                'product' => $product,
                'size' => $line['size'],
                'quantity' => $line['quantity'],
                'line_total' => (float) $product->price * $line['quantity'],
            ];
        })->filter()->values();
    }

    public function create()
    {
        $items = $this->buildCartItems();
        $cartTotal = $items->sum('line_total');

        return view('checkout.create', compact('items', 'cartTotal'));
    }

    public function previewTotals(Request $request)
    {
        $validated = $request->validate([
            'promocode' => ['nullable', 'string', 'max:50'],
        ]);

        $items = $this->buildCartItems();
        $subtotal = (float) $items->sum('line_total');
        $preview = PromocodePricing::preview($subtotal, (string) ($validated['promocode'] ?? ''));

        return response()->json([
            'empty_cart' => $items->isEmpty(),
            'subtotal' => round($preview['subtotal'], 2),
            'discount' => round($preview['discount'], 2),
            'total' => round($preview['total'], 2),
            'promocode' => [
                'valid' => $preview['promocode_valid'],
                'code' => $preview['promocode_code'],
                'message' => $preview['message'],
            ],
        ]);
    }

    public function addressSuggestions(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:3'],
        ]);

        return response()->json(
            $this->daDataService->suggestAddress($request->string('query')->toString())
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => ['required', 'array'],
            'address.full' => ['required', 'string'],
            'promocode' => ['nullable', 'string'],
        ]);

        $items = $this->buildCartItems();

        if ($items->isEmpty()) {
            return back()->withErrors(['cart' => 'Корзина пуста. Добавьте товары перед оформлением.']);
        }

        foreach ($items as $item) {
            if ($item['product']->stock < $item['quantity']) {
                return back()->withErrors([
                    'stock' => "Недостаточно товара \"{$item['product']->name}\" на складе. Обновите корзину.",
                ]);
            }
        }

        $subtotal = (float) $items->sum('line_total');
        $promocode = null;
        $total = $subtotal;

        $rawPromo = trim((string) ($validated['promocode'] ?? ''));
        if ($rawPromo !== '') {
            $promocode = PromocodePricing::findActiveByCode($rawPromo);
            if (! $promocode || ! PromocodePricing::redeemable($promocode)) {
                return back()->withErrors([
                    'promocode' => 'Промокод не найден, истёк или недоступен.',
                ]);
            }
            $total = max(0.0, $subtotal - PromocodePricing::discountAmount($subtotal, $promocode));
            $promocode->increment('usage_count');
        }

        $order = Order::query()->create([
            'user_id' => Auth::id(),
            'total_price' => $total,
            'status' => 'pending',
            'address' => $validated['address'],
            'promocode_id' => $promocode?->id,
        ]);

        foreach ($items as $item) {
            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'size' => $item['size'],
                'quantity' => $item['quantity'],
                'price' => $item['product']->price,
            ]);
        }

        $payment = $this->yooKassaService->createPayment($order);

        if (! $payment) {
            return back()->withErrors([
                'payment' => 'Не удалось создать ссылку оплаты YooMoney. Проверьте YOOKASSA_SHOP_ID/YOOKASSA_SECRET_KEY и доступ к API.',
            ]);
        }

        $order->update([
            'yookassa_payment_id' => $payment['id'] ?? null,
        ]);

        session()->forget('cart');

        return redirect($payment['confirmation']['confirmation_url']);
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if ($order->status === 'pending' && $order->yookassa_payment_id) {
            $payment = $this->yooKassaService->fetchPayment($order->yookassa_payment_id);
            if (($payment['status'] ?? null) === 'succeeded') {
                $this->markOrderPaid($order);
                $order->refresh();
            }
        }

        return view('checkout.success', compact('order'));
    }

    private function markOrderPaid(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            /** @var Order|null $locked */
            $locked = Order::query()->with('items')->lockForUpdate()->find($order->id);
            if (! $locked || $locked->status !== 'pending') {
                return;
            }

            foreach ($locked->items as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if (! $product || $product->stock < 1) {
                    continue;
                }

                $decreaseBy = min($product->stock, (int) $item->quantity);
                $product->decrement('stock', $decreaseBy);
            }

            $locked->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });
    }
}
