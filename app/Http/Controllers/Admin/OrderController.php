<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

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

        $order->update(['status' => $validated['status']]);

        return back()->with('status', 'Статус заказа обновлен.');
    }
}
