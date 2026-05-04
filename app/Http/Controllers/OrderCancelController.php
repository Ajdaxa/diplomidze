<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderCancelController extends Controller
{
    public function store(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if (! $order->canCancel()) {
            return back()->with('status_type', 'error')->with('status', 'Этот заказ нельзя отменить.');
        }

        Order::cancelUnfinished($order);

        return back()->with('status', 'Заказ отменён.');
    }
}
