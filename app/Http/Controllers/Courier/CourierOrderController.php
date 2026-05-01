<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierOrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->where('courier_id', Auth::id())
            ->latest()
            ->get();

        return view('courier.orders.index', compact('orders'));
    }

    public function arrived(Order $order, TelegramService $telegramService)
    {
        $this->authorizeCourierOrder($order);

        $order->update(['status' => 'arrived']);

        if ($order->user?->telegram_chat_id) {
            $telegramService->courierArrived($order->user->telegram_chat_id);
        }

        return back()->with('status', 'Статус обновлен: на месте.');
    }

    public function delivered(Order $order, TelegramService $telegramService)
    {
        $this->authorizeCourierOrder($order);

        $order->update(['status' => 'delivered']);

        if ($order->user?->telegram_chat_id) {
            $telegramService->orderDelivered($order->user->telegram_chat_id);
        }

        return back()->with('status', 'Заказ завершен.');
    }

    private function authorizeCourierOrder(Order $order): void
    {
        abort_unless($order->courier_id === Auth::id(), 403);
    }
}
