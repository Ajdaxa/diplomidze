@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        <x-page-heading title="Профиль" lede="Данные аккаунта и история заказов" />

        <div class="mb-8 rounded-2xl border border-neutral-200 bg-gradient-to-br from-neutral-900 to-neutral-800 p-5 text-white sm:p-6">
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-white/70">Баллы за покупки</p>
            <p class="mt-3 text-3xl font-light tabular-nums">{{ number_format($user->loyalty_points, 0, '.', ' ') }} <span class="text-lg">баллов</span></p>
            <p class="mt-2 max-w-md text-sm text-white/85">5% от суммы каждого оплаченного заказа. При оформлении можно списать все баллы разом — до 30% от суммы заказа (1 балл = 1 ₽).</p>
        </div>

        <div class="mb-8 rounded-2xl border border-neutral-200 bg-white p-4 sm:mb-10 sm:p-6">
            <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-neutral-500">Контакты</h2>
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-neutral-500">Имя</dt><dd class="font-medium">{{ $user->name }}</dd></div>
                <div><dt class="text-neutral-500">Email</dt><dd class="font-medium">{{ $user->email ?: '—' }}</dd></div>
                <div><dt class="text-neutral-500">Телефон</dt><dd class="font-medium">{{ $user->phone ?: '—' }}</dd></div>
            </dl>
            <form method="POST" action="{{ route('logout') }}" class="mt-5">
                @csrf
                <button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-neutral-300 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-neutral-700 hover:border-neutral-900 hover:text-black">
                    Выйти из аккаунта
                </button>
            </form>
        </div>

        <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-neutral-500">Заказы</h2>
        <div class="space-y-4">
            @forelse($user->clientOrders as $order)
                <div class="rounded-2xl border border-neutral-200 bg-white p-4 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-neutral-100 pb-4">
                        <div>
                            <p class="font-semibold">Заказ #{{ $order->id }}</p>
                            <p class="text-xs text-neutral-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold">{{ number_format($order->total_price, 2, '.', ' ') }} ₽</p>
                            @php
                                $label = \App\Support\OrderStatus::label($order->status);
                                $classes = \App\Support\OrderStatus::badgeClass($order->status);
                            @endphp
                            <p class="inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold tracking-wider {{ $classes }}">{{ $label }}</p>
                        </div>
                    </div>
                    @if($order->promocode)
                        <p class="mt-3 text-xs text-neutral-500">Промокод: {{ $order->promocode->code }}</p>
                    @endif
                    @if($order->courier)
                        <p class="text-xs text-neutral-500">Курьер: {{ $order->courier->name }}</p>
                    @endif
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach($order->items as $item)
                            <li class="flex justify-between gap-4">
                                <span>{{ $item->product?->name ?? 'Товар' }} <span class="text-neutral-500">· {{ $item->size }} · ×{{ $item->quantity }}</span></span>
                                <span>{{ number_format($item->price * $item->quantity, 2, '.', ' ') }} ₽</span>
                            </li>
                        @endforeach
                    </ul>
                    @if(is_array($order->address))
                        <p class="mt-4 text-xs text-neutral-500">Адрес: {{ $order->address['full'] ?? json_encode($order->address) }}</p>
                    @endif
                    @if(! in_array($order->status, ['delivered', 'cancelled'], true))
                        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-4" onsubmit="return confirm('Отменить заказ?');">
                            @csrf
                            <button type="submit" class="rounded border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-800 hover:bg-rose-100">Отменить заказ</button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-sm text-neutral-500">Пока нет заказов.</p>
            @endforelse
        </div>
    </div>
@endsection
