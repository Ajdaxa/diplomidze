<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

    public function create()
    {
        return view('checkout.create');
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
            'total_price' => ['required', 'numeric', 'min:1'],
            'promocode' => ['nullable', 'string'],
        ]);

        $promocode = null;
        $total = (float) $validated['total_price'];

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

        $payment = $this->yooKassaService->createPayment($order);

        if (! $payment) {
            return back()->withErrors(['payment' => 'Не удалось создать ссылку оплаты.']);
        }

        $order->update([
            'yookassa_payment_id' => $payment['id'] ?? null,
        ]);

        return redirect($payment['confirmation']['confirmation_url']);
    }

    public function success(Order $order)
    {
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
