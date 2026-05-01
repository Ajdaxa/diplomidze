<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class YooKassaService
{
    public function createPayment(Order $order): ?array
    {
        $shopId = config('services.yookassa.shop_id');
        $secret = config('services.yookassa.secret_key');

        if (! $shopId || ! $secret) {
            return null;
        }

        $response = Http::withBasicAuth($shopId, $secret)
            ->withHeaders(['Idempotence-Key' => (string) Str::uuid()])
            ->post('https://api.yookassa.ru/v3/payments', [
                'amount' => [
                    'value' => number_format((float) $order->total_price, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'capture' => true,
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => route('checkout.success', $order),
                ],
                'description' => "Оплата заказа #{$order->id} в ДЯБ",
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}
