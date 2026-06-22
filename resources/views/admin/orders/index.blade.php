@extends('layouts.admin')

@section('title', 'Заказы')
@section('heading', 'Заказы')

@section('content')
    <div class="space-y-4">
        @foreach($orders as $order)
            <div class="rounded-xl border border-stone-200 bg-white p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium">
                            <a href="{{ route('admin.orders.show', $order) }}" class="underline decoration-stone-300 underline-offset-2 hover:decoration-black">Заказ #{{ $order->id }}</a>
                            — {{ number_format($order->total_price, 2, '.', ' ') }} ₽
                        </p>
                        <p class="text-sm text-stone-500">
                            Клиент: {{ $order->user?->name }} • Статус: {{ \App\Support\OrderStatus::label($order->status) }}
                            @if($order->leave_at_door)
                                <span class="ml-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-amber-800">У двери</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex min-w-0 flex-wrap gap-2 overflow-x-auto pb-1 sm:flex-nowrap sm:pb-0">
                        <form method="POST" action="{{ route('admin.orders.assign-courier', $order) }}" class="flex min-w-0 flex-wrap items-center gap-2">
                            @csrf @method('PATCH')
                            <select name="courier_id" class="rounded border border-stone-300 px-2 py-1 text-sm">
                                @foreach($couriers as $courier)
                                    <option value="{{ $courier->id }}" @selected($order->courier_id === $courier->id)>{{ $courier->name }}</option>
                                @endforeach
                            </select>
                            <button class="rounded border border-stone-300 px-3 py-1 text-sm">Назначить</button>
                        </form>
                        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex min-w-0 flex-wrap items-center gap-2">
                            @csrf @method('PATCH')
                            <select name="status" class="rounded border border-stone-300 px-2 py-1 text-sm">
                                @foreach(\App\Support\OrderStatus::LABELS as $status => $statusLabel)
                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ $statusLabel }}</option>
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
