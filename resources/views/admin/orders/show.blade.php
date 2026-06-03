@extends('layouts.admin')

@section('title', 'Заказ #'.$order->id)
@section('heading', 'Заказ #'.$order->id)

@section('content')
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-neutral-600 underline hover:text-black">← К списку заказов</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Сводка</h2>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-stone-500">Статус</dt><dd class="font-medium">{{ \App\Support\OrderStatus::label($order->status) }}</dd></div>
                    <div><dt class="text-stone-500">Сумма</dt><dd class="font-medium">{{ number_format($order->total_price, 2, '.', ' ') }} ₽</dd></div>
                    <div><dt class="text-stone-500">Создан</dt><dd class="font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</dd></div>
                    <div><dt class="text-stone-500">Оплачен</dt><dd class="font-medium">{{ $order->paid_at?->format('d.m.Y H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-stone-500">YooKassa payment id</dt><dd class="break-all font-mono text-xs">{{ $order->yookassa_payment_id ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Клиент</h2>
                @if($order->user)
                    <p class="mt-3 font-medium">
                        @if($order->user->isStoreClient())
                            <a href="{{ route('admin.users.show', $order->user) }}" class="underline decoration-stone-300 underline-offset-2 hover:decoration-black">{{ $order->user->name }}</a>
                        @else
                            {{ $order->user->name }}
                        @endif
                    </p>
                    <p class="text-sm text-stone-600">{{ $order->user->email ?: '—' }}</p>
                    <p class="text-sm text-stone-600">{{ $order->user->phone ?: '—' }}</p>
                @else
                    <p class="mt-3 text-sm text-stone-500">Пользователь удалён</p>
                @endif
            </div>

            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Адрес доставки</h2>
                @if(is_array($order->address))
                    <p class="mt-3 text-sm">{{ $order->address['full'] ?? json_encode($order->address, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</p>
                @else
                    <p class="mt-3 text-sm text-stone-500">—</p>
                @endif
            </div>

            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Состав заказа</h2>
                <ul class="mt-4 divide-y divide-stone-100">
                    @foreach($order->items as $item)
                        <li class="flex flex-wrap justify-between gap-2 py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ $item->product?->name ?? 'Товар #'.$item->product_id }}</p>
                                <p class="text-xs text-stone-500">{{ $item->size }} × {{ $item->quantity }} · {{ number_format($item->price, 2, '.', ' ') }} ₽ / шт.</p>
                            </div>
                            <p class="font-semibold">{{ number_format($item->price * $item->quantity, 2, '.', ' ') }} ₽</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Промокод</h2>
                @if($order->promocode)
                    <p class="mt-3 font-mono text-sm">{{ $order->promocode->code }}</p>
                    <p class="text-xs text-stone-500">{{ $order->promocode->type }} · {{ $order->promocode->value }}</p>
                @else
                    <p class="mt-3 text-sm text-stone-500">Не применялся</p>
                @endif
            </div>

            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Курьер</h2>
                @if($order->courier)
                    <p class="mt-3 font-medium">{{ $order->courier->name }}</p>
                    <p class="text-xs text-stone-500">{{ $order->courier->phone ?? '' }}</p>
                @else
                    <p class="mt-3 text-sm text-stone-500">Не назначен</p>
                @endif

                <form method="POST" action="{{ route('admin.orders.assign-courier', $order) }}" class="mt-4 flex flex-col gap-2">
                    @csrf @method('PATCH')
                    <select name="courier_id" class="rounded border border-stone-300 px-2 py-2 text-sm">
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" @selected($order->courier_id === $courier->id)>{{ $courier->name }}</option>
                        @endforeach
                    </select>
                    <button class="rounded bg-stone-900 px-3 py-2 text-sm text-white">Назначить</button>
                </form>
            </div>

            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Статус</h2>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 flex flex-col gap-2">
                    @csrf @method('PATCH')
                    <select name="status" class="rounded border border-stone-300 px-2 py-2 text-sm">
                        @foreach(\App\Support\OrderStatus::LABELS as $status => $statusLabel)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                    <button class="rounded border border-stone-300 px-3 py-2 text-sm">Обновить статус</button>
                </form>
            </div>
        </div>
    </div>
@endsection
