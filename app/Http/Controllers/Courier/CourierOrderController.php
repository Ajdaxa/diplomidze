<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourierOrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->where('courier_id', Auth::id())
            ->with(['items.product', 'user'])
            ->latest()
            ->get();

        return view('courier.orders.index', compact('orders'));
    }

    public function arrived(Order $order)
    {
        $this->authorizeCourierOrder($order);
        $this->abortIfTerminal($order);

        $order->update(['status' => 'arrived']);

        return back()->with('status', 'Статус обновлен: на месте.');
    }

    public function delivered(Request $request, Order $order)
    {
        $this->authorizeCourierOrder($order);
        $this->abortIfTerminal($order);

        $rules = [
            'delivery_photo' => [$order->requiresDoorPhoto() ? 'required' : 'nullable', 'image', 'max:5120'],
        ];

        $validated = $request->validate($rules, [
            'delivery_photo.required' => 'Для заказа «у двери» нужно приложить фото доставки.',
            'delivery_photo.image' => 'Файл должен быть изображением.',
            'delivery_photo.max' => 'Фото не должно превышать 5 МБ.',
        ]);

        $photoPath = null;
        if ($request->hasFile('delivery_photo')) {
            $photoPath = $request->file('delivery_photo')->store('deliveries', 'public');
        }

        DB::transaction(function () use ($order, $photoPath): void {
            /** @var Order|null $locked */
            $locked = Order::query()->with('items')->lockForUpdate()->find($order->id);
            if (! $locked) {
                return;
            }

            if ($locked->status === 'pending') {
                foreach ($locked->items as $item) {
                    $product = Product::query()->lockForUpdate()->find($item->product_id);
                    if (! $product || $product->stock < 1) {
                        continue;
                    }
                    $product->decrement('stock', min($product->stock, (int) $item->quantity));
                }
            }

            $locked->update([
                'status' => 'delivered',
                'delivery_photo' => $photoPath ?? $locked->delivery_photo,
            ]);
        });

        return back()->with('status', 'Заказ завершен.');
    }

    private function authorizeCourierOrder(Order $order): void
    {
        abort_unless($order->courier_id === Auth::id(), 403);
    }

    private function abortIfTerminal(Order $order): void
    {
        abort_if(in_array($order->status, ['cancelled', 'delivered'], true), 403);
    }
}
