@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-xl rounded-xl border border-stone-200 bg-white p-6 text-center">
        <h1 class="mb-2 text-2xl font-semibold">Заказ оформлен</h1>
        <p class="text-stone-600">Заказ #{{ $order->id }} создан.</p>
        <p class="mt-2 text-sm text-stone-500">Текущий статус: {{ \App\Support\OrderStatus::label($order->status) }}.</p>
        <p class="mt-2 text-xs text-stone-500">Если вы уже оплатили, статус может обновиться через несколько секунд.</p>
    </div>
@endsection
