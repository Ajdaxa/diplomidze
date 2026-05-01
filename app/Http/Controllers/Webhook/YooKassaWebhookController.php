<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class YooKassaWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramService $telegramService)
    {
        $event = $request->input('event');
        $paymentId = $request->input('object.id');

        if ($event !== 'payment.succeeded' || ! $paymentId) {
            return response()->json(['ok' => true]);
        }

        $order = Order::query()->where('yookassa_payment_id', $paymentId)->first();

        if ($order && $order->status === 'pending') {
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            if ($order->user?->telegram_chat_id) {
                $telegramService->orderPaid($order->user->telegram_chat_id, $order->id);
            }
        }

        return response()->json(['ok' => true]);
    }
}
