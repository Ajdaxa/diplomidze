<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class YooKassaWebhookController extends Controller
{
    private const LOG = 'yookassa.webhook';

    public function __invoke(Request $request, LoyaltyService $loyaltyService)
    {
        $body = $request->all();
        $event = data_get($body, 'event');
        $paymentId = data_get($body, 'object.id')
            ?: data_get($body, 'object.payment_id');

        Log::channel('single')->info(self::LOG.' received', [
            'event' => $event,
            'payment_id' => $paymentId,
            'object_status' => data_get($body, 'object.status'),
            'metadata' => data_get($body, 'object.metadata'),
            'ip' => $request->ip(),
        ]);

        $objectStatus = data_get($body, 'object.status');
        $isSucceeded = $event === 'payment.succeeded' || $objectStatus === 'succeeded';

        if (! $isSucceeded || ! $paymentId) {
            Log::channel('single')->info(self::LOG.' ignored', [
                'reason' => 'not_succeeded_or_no_payment_id',
                'event' => $event,
                'object_status' => $objectStatus,
                'payment_id' => $paymentId,
            ]);

            return response()->json(['ok' => true]);
        }

        $order = Order::query()->with(['items', 'user'])->where('yookassa_payment_id', $paymentId)->first();

        if (! $order) {
            Log::channel('single')->warning(self::LOG.' order_not_found', [
                'payment_id' => $paymentId,
                'metadata_order_id' => data_get($body, 'object.metadata.order_id'),
            ]);

            return response()->json(['ok' => true]);
        }

        Log::channel('single')->info(self::LOG.' order_resolved', [
            'order_id' => $order->id,
            'status' => $order->status,
            'yookassa_payment_id' => $order->yookassa_payment_id,
            'items_count' => $order->items->count(),
        ]);

        if ($order->status !== 'pending') {
            Log::channel('single')->info(self::LOG.' skip_already_processed', [
                'order_id' => $order->id,
                'status' => $order->status,
            ]);

            return response()->json(['ok' => true]);
        }

        try {
            DB::transaction(function () use ($order, $loyaltyService): void {
                $order->refresh();
                if ($order->status !== 'pending') {
                    Log::channel('single')->info(self::LOG.' race_skip_not_pending', [
                        'order_id' => $order->id,
                        'status' => $order->status,
                    ]);

                    return;
                }

                foreach ($order->items as $item) {
                    $product = Product::query()->lockForUpdate()->find($item->product_id);
                    if (! $product || $product->stock < 1) {
                        Log::channel('single')->warning(self::LOG.' stock_skip', [
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'qty' => $item->quantity,
                            'stock' => $product?->stock,
                        ]);

                        continue;
                    }

                    $decreaseBy = min($product->stock, (int) $item->quantity);
                    $product->decrement('stock', $decreaseBy);
                }

                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                $order->refresh(['user']);

                $loyaltyService->awardForPaidOrder($order);

                Log::channel('single')->info(self::LOG.' processed_ok', [
                    'order_id' => $order->id,
                    'payment_id' => $order->yookassa_payment_id,
                ]);
            });
        } catch (Throwable $e) {
            Log::channel('single')->error(self::LOG.' transaction_failed', [
                'order_id' => $order->id,
                'payment_id' => $paymentId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['ok' => false], 500);
        }

        return response()->json(['ok' => true]);
    }
}
