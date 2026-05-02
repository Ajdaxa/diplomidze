<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()->with(['user', 'courier', 'items.product'])->latest()->paginate(20);
        $couriers = User::role('courier')->get();

        return view('admin.orders.index', compact('orders', 'couriers'));
    }

    public function assignCourier(Request $request, Order $order)
    {
        $validated = $request->validate([
            'courier_id' => ['required', 'exists:users,id'],
        ]);

        $order->update([
            'courier_id' => $validated['courier_id'],
            'status' => $order->status === 'paid' ? 'in_delivery' : $order->status,
        ]);

        return back()->with('status', 'Курьер назначен.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,in_delivery,arrived,delivered'],
        ]);

        $this->applyStatusWithInventory($order, $validated['status']);

        return back()->with('status', 'Статус заказа обновлен.');
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
