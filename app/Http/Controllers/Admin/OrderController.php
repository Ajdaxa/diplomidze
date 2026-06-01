<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\TelegramService;
use App\Support\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    public function index()
    {
        $orders = Order::query()->with(['user', 'courier', 'items.product'])->latest()->paginate(20);
        $couriers = User::role('courier')->get();

        return view('admin.orders.index', compact('orders', 'couriers'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'courier', 'promocode', 'items.product']);
        $couriers = User::role('courier')->get();

        return view('admin.orders.show', compact('order', 'couriers'));
    }

    public function assignCourier(Request $request, Order $order)
    {
        abort_if($order->status === 'cancelled', 403);

        $validated = $request->validate([
            'courier_id' => ['required', 'exists:users,id'],
        ]);

        $newStatus = $order->status === 'paid' ? 'in_delivery' : $order->status;
        $order->update([
            'courier_id' => $validated['courier_id'],
            'status' => $newStatus,
        ]);

        $this->notifyCustomerStatus($order->fresh(['user']), $newStatus);

        return back()->with('status', 'Курьер назначен.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,in_delivery,arrived,delivered,cancelled'],
        ]);

        if ($validated['status'] === 'cancelled') {
            Order::cancelUnfinished($order);

            return back()->with('status', 'Заказ отменён.');
        }

        $this->applyStatusWithInventory($order, $validated['status']);
        $this->notifyCustomerStatus($order->fresh(['user']), $validated['status']);

        return back()->with('status', 'Статус заказа обновлен.');
    }

    private function notifyCustomerStatus(Order $order, string $status): void
    {
        $chatId = $order->user?->telegram_chat_id;
        if (! $chatId || in_array($status, ['pending', 'cancelled'], true)) {
            return;
        }

        $this->telegramService->orderStatusForCustomer(
            (string) $chatId,
            $order->id,
            OrderStatus::label($status)
        );
    }

    private function applyStatusWithInventory(Order $order, string $newStatus): void
    {
        DB::transaction(function () use ($order, $newStatus): void {
            /** @var Order|null $locked */
            $locked = Order::query()->with('items')->lockForUpdate()->find($order->id);
            if (! $locked) {
                return;
            }

            if ($locked->status === 'pending' && $newStatus !== 'pending') {
                foreach ($locked->items as $item) {
                    $product = Product::query()->lockForUpdate()->find($item->product_id);
                    if (! $product || $product->stock < 1) {
                        continue;
                    }
                    $product->decrement('stock', min($product->stock, (int) $item->quantity));
                }
            }

            $payload = ['status' => $newStatus];
            if ($newStatus === 'paid' && ! $locked->paid_at) {
                $payload['paid_at'] = now();
            }

            $locked->update($payload);
        });
    }
}
