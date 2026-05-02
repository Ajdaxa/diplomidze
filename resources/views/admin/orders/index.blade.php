@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">Заказы</h1>
    <div class="space-y-4">
        @foreach($orders as $order)
            <div class="rounded-xl border border-stone-200 bg-white p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium">Заказ #{{ $order->id }} — {{ number_format($order->total_price, 2, '.', ' ') }} ₽</p>
                        <p class="text-sm text-stone-500">Клиент: {{ $order->user?->name }} • Статус: {{ $order->status }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.orders.assign-courier', $order) }}">
                            @csrf @method('PATCH')
                            <select name="courier_id" class="rounded border border-stone-300 px-2 py-1 text-sm">
                                @foreach($couriers as $courier)
                                    <option value="{{ $courier->id }}" @selected($order->courier_id === $courier->id)>{{ $courier->name }}</option>
                                @endforeach
                            </select>
                            <button class="rounded border border-stone-300 px-3 py-1 text-sm">Назначить</button>
                        </form>
                        <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                            @csrf @method('PATCH')
                            <select name="status" class="rounded border border-stone-300 px-2 py-1 text-sm">
                                @foreach(['pending','paid','in_delivery','arrived','delivered'] as $status)
                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                            <button class="rounded border border-stone-300 px-3 py-1 text-sm">Обновить</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
