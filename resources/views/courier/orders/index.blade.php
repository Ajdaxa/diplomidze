@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">Кабинет курьера</h1>
    <div class="space-y-4">
        @forelse($orders as $order)
            @php
                $addr = is_array($order->address) ? ($order->address['full'] ?? null) : null;
                $clientPhone = $order->user?->phone;
                $terminal = in_array($order->status, ['cancelled', 'delivered'], true);
            @endphp
            <div class="rounded-xl border border-stone-200 bg-white p-4">
                <p class="font-medium">Заказ #{{ $order->id }}</p>
                <p class="text-sm text-stone-500">Статус: {{ \App\Support\OrderStatus::label($order->status) }}</p>
                <p class="mt-2 text-sm font-semibold">{{ number_format($order->total_price, 2, '.', ' ') }} ₽</p>
                @if($order->user)
                    <p class="mt-2 text-sm">Клиент: <span class="font-medium">{{ $order->user->name }}</span></p>
                    @if($clientPhone)
                        <p class="text-sm text-stone-600"><a href="tel:{{ preg_replace('/\s+/', '', $clientPhone) }}" class="underline">{{ $clientPhone }}</a></p>
                    @endif
                @endif
                @if($addr)
                    <p class="mt-2 text-sm text-stone-800"><span class="text-stone-500">Адрес:</span> {{ $addr }}</p>
                @elseif(is_array($order->address))
                    <p class="mt-2 text-xs text-stone-500">{{ json_encode($order->address, JSON_UNESCAPED_UNICODE) }}</p>
                @endif
                <ul class="mt-3 space-y-1 border-t border-stone-100 pt-3 text-sm">
                    @foreach($order->items as $item)
                        <li class="flex justify-between gap-2">
                            <span>{{ $item->product?->name ?? 'Товар' }} <span class="text-stone-500">· {{ $item->size }} · ×{{ $item->quantity }}</span></span>
                            <span>{{ number_format($item->price * $item->quantity, 2, '.', ' ') }} ₽</span>
                        </li>
                    @endforeach
                </ul>
                @unless($terminal)
                    <div class="mt-3 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('courier.orders.arrived', $order) }}">
                            @csrf
                            <button type="submit" class="rounded bg-stone-900 px-3 py-2 text-sm text-white">Я на месте</button>
                        </form>
                        <form method="POST" action="{{ route('courier.orders.delivered', $order) }}">
                            @csrf
                            <button type="submit" class="rounded border border-stone-300 px-3 py-2 text-sm">Доставлено</button>
                        </form>
                    </div>
                @endunless
            </div>
        @empty
            <p class="text-stone-500">Нет назначенных заказов.</p>
        @endforelse
    </div>
@endsection
