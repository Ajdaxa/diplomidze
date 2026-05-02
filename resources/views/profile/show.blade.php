@extends('layouts.app')

@section('content')
    @php
        $statusMap = [
            'pending' => ['Ожидает оплату', 'bg-amber-50 text-amber-700 border-amber-200'],
            'paid' => ['Оплачен', 'bg-sky-50 text-sky-700 border-sky-200'],
            'in_delivery' => ['В доставке', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
            'arrived' => ['Курьер на месте', 'bg-violet-50 text-violet-700 border-violet-200'],
            'delivered' => ['Доставлен', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        ];
    @endphp
    <div class="mx-auto max-w-3xl">
        <h1 class="mb-2 text-3xl font-semibold">Профиль</h1>
        <p class="mb-8 text-sm text-stone-500">Данные аккаунта и история заказов</p>

        <div class="mb-10 rounded-2xl border border-stone-200 bg-white p-6">
            <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-stone-500">Контакты</h2>
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-stone-500">Имя</dt><dd class="font-medium">{{ $user->name }}</dd></div>
                <div><dt class="text-stone-500">Email</dt><dd class="font-medium">{{ $user->email ?: '—' }}</dd></div>
                <div><dt class="text-stone-500">Телефон</dt><dd class="font-medium">{{ $user->phone ?: '—' }}</dd></div>
                <div><dt class="text-stone-500">Telegram</dt><dd class="font-medium">{{ $user->telegram_chat_id ? 'Привязан' : 'Не привязан' }}</dd></div>
            </dl>
        </div>

        <h2 class="mb-4 text-xs font-semibold uppercase tracking-widest text-stone-500">Заказы</h2>
        <div class="space-y-4">
            @forelse($user->clientOrders as $order)
                <div class="rounded-2xl border border-stone-200 bg-white p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-stone-100 pb-4">
                        <div>
                            <p class="font-semibold">Заказ #{{ $order->id }}</p>
                            <p class="text-xs text-stone-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold">{{ number_format($order->total_price, 2, '.', ' ') }} ₽</p>
                            @php
                                [$label, $classes] = $statusMap[$order->status] ?? [strtoupper((string) $order->status), 'bg-stone-100 text-stone-700 border-stone-200'];
                            @endphp
                            <p class="inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider {{ $classes }}">{{ $label }}</p>
                        </div>
                    </div>
                    @if($order->promocode)
                        <p class="mt-3 text-xs text-stone-500">Промокод: {{ $order->promocode->code }}</p>
                    @endif
                    @if($order->courier)
                        <p class="text-xs text-stone-500">Курьер: {{ $order->courier->name }}</p>
                    @endif
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach($order->items as $item)
                            <li class="flex justify-between gap-4">
                                <span>{{ $item->product?->name ?? 'Товар' }} <span class="text-stone-500">· {{ $item->size }} · ×{{ $item->quantity }}</span></span>
                                <span>{{ number_format($item->price * $item->quantity, 2, '.', ' ') }} ₽</span>
                            </li>
                        @endforeach
                    </ul>
                    @if(is_array($order->address))
                        <p class="mt-4 text-xs text-stone-500">Адрес: {{ $order->address['full'] ?? json_encode($order->address) }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-stone-500">Пока нет заказов.</p>
            @endforelse
        </div>
    </div>
@endsection
