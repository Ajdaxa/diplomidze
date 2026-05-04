<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class YooKassaService
{
    private function credentials(): ?array
    {
        $shopId = config('services.yookassa.shop_id');
        $secret = config('services.yookassa.secret_key');

        if (! $shopId || ! $secret) {
            return null;
        }

        return [$shopId, $secret];
    }

    public function createPayment(Order $order): ?array
    {
        $credentials = $this->credentials();
        if (! $credentials) {
            return null;
        }
        [$shopId, $secret] = $credentials;

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
            Log::warning('YooKassa createPayment failed', [
                'status' => $response->status(),
                'body' => $response->json(),
                'order_id' => $order->id,
            ]);

            return null;
        }

        return $response->json();
    }

    public function fetchPayment(string $paymentId): ?array
    {
        $credentials = $this->credentials();
        if (! $credentials) {
            return null;
        }
        [$shopId, $secret] = $credentials;

        $response = Http::withBasicAuth($shopId, $secret)
            ->get('https://api.yookassa.ru/v3/payments/'.$paymentId);

        if ($response->failed()) {
            Log::warning('YooKassa fetchPayment failed', [
                'status' => $response->status(),
                'payment_id' => $paymentId,
                'body' => $response->json(),
            ]);

            return null;
        }

        return $response->json();
    }
}
