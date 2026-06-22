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
                    <div><dt class="text-stone-500">Доставка</dt><dd class="font-medium">{{ $order->leave_at_door ? 'У двери' : 'Обычная' }}</dd></div>
                    <div><dt class="text-stone-500">YooKassa payment id</dt><dd class="break-all font-mono text-xs">{{ $order->yookassa_payment_id ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-xl border border-stone-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Клиент</h2>
                @if($order->user)
                    <p class="mt-3 font-medium">
                        @if($isAdmin && $order->user->isStoreClient())
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

                @if($order->leave_at_door)
                    <div class="mt-4 flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50/80 px-4 py-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-amber-700 ring-1 ring-amber-100">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-amber-950">Оставить у двери</p>
                            <p class="mt-0.5 text-xs text-amber-800/90">Клиент выбрал бесконтактную доставку у двери</p>
                        </div>
                    </div>
                @endif
            </div>

            @if($order->status === 'delivered' && $order->delivery_photo)
                <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
                    <div class="border-b border-stone-100 px-5 py-4">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Подтверждение доставки</h2>
                        <p class="mt-1 text-sm text-stone-600">Фото заказа у двери, приложенное курьером</p>
                    </div>
                    <a href="{{ $order->deliveryPhotoUrl() }}" target="_blank" rel="noopener" class="group block">
                        <img
                            src="{{ $order->deliveryPhotoUrl() }}"
                            alt="Фото доставки заказа #{{ $order->id }}"
                            class="max-h-[28rem] w-full object-cover transition duration-300 group-hover:scale-[1.01]"
                        >
                    </a>
                    <div class="flex items-center justify-between gap-3 border-t border-stone-100 px-5 py-3 text-xs text-stone-500">
                        <span>Нажмите на фото, чтобы открыть в полном размере</span>
                        <a href="{{ $order->deliveryPhotoUrl() }}" target="_blank" rel="noopener" class="font-medium text-stone-800 underline decoration-stone-300 underline-offset-2 hover:decoration-black">Открыть</a>
                    </div>
                </div>
            @elseif($order->leave_at_door && $order->status !== 'delivered')
                <div class="rounded-xl border border-dashed border-stone-200 bg-stone-50 p-5">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Подтверждение доставки</h2>
                    <p class="mt-2 text-sm text-stone-500">Фото появится здесь после завершения доставки курьером</p>
                </div>
            @endif

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
