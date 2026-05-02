<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promocode;
use App\Services\DaDataService;
use App\Services\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $promocode = null;
        $total = (float) $items->sum('line_total');

        if (! empty($validated['promocode'])) {
            $promocode = Promocode::query()
                ->where('code', $validated['promocode'])
                ->where('is_active', true)
                ->first();

            if ($promocode) {
                $total = max(0, $this->applyPromocode($total, $promocode));
                $promocode->increment('usage_count');
            }
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

        return view('checkout.success', compact('order'));
    }

    private function applyPromocode(float $total, Promocode $promocode): float
    {
        if ($promocode->type === 'percent') {
            $discount = $total * ((float) $promocode->value / 100);
        } else {
            $discount = (float) $promocode->value;
        }

        if ($promocode->max_discount) {
            $discount = min($discount, (float) $promocode->max_discount);
        }

        return $total - $discount;
    }
}
