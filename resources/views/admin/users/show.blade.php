@extends('layouts.admin')

@section('title', $user->name)
@section('heading', $user->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-neutral-600 underline hover:text-black">← К списку клиентов</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Редактирование</h2>
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-neutral-500">Имя</label>
                        <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                        @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-neutral-500">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                            @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-neutral-500">Телефон</label>
                            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                            @error('phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="loyalty_points" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-neutral-500">Баллы лояльности</label>
                        <input id="loyalty_points" name="loyalty_points" type="number" min="0" value="{{ old('loyalty_points', $user->loyalty_points) }}" required class="w-full max-w-xs rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                        @error('loyalty_points')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="border-t border-neutral-100 pt-4">
                        <p class="mb-3 text-xs text-neutral-500">Новый пароль — только если нужно сбросить доступ клиенту. Оставьте пустым, чтобы не менять.</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-neutral-500">Пароль</label>
                                <input id="password" name="password" type="password" autocomplete="new-password" class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                                @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-neutral-500">Повтор пароля</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="rounded-lg bg-black px-5 py-2.5 text-xs font-semibold uppercase tracking-wider text-white">Сохранить</button>
                </form>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Последние заказы</h2>
                @if($orders->isEmpty())
                    <p class="mt-4 text-sm text-neutral-500">Заказов пока нет.</p>
                @else
                    <ul class="mt-4 divide-y divide-neutral-100">
                        @foreach($orders as $order)
                            <li class="flex flex-wrap items-center justify-between gap-2 py-3 text-sm">
                                <div>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-medium underline decoration-neutral-300 underline-offset-2 hover:decoration-black">
                                        Заказ #{{ $order->id }}
                                    </a>
                                    <p class="text-xs text-neutral-500">{{ $order->created_at->format('d.m.Y H:i') }} · {{ $order->items_count }} поз.</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold tabular-nums">{{ number_format($order->total_price, 0, '.', ' ') }} ₽</p>
                                    <span class="inline-block rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase {{ \App\Support\OrderStatus::badgeClass($order->status) }}">
                                        {{ \App\Support\OrderStatus::label($order->status) }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Сводка</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-neutral-500">ID</dt>
                        <dd class="font-medium tabular-nums">{{ $user->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Роль</dt>
                        <dd class="font-medium">{{ $user->roleLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">На сайте с</dt>
                        <dd class="font-medium">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Заказов всего</dt>
                        <dd class="font-medium tabular-nums">{{ $user->client_orders_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Сумма оплаченных</dt>
                        <dd class="font-medium tabular-nums">{{ number_format($ordersTotal, 0, '.', ' ') }} ₽</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">Отзывов</dt>
                        <dd class="font-medium tabular-nums">{{ $user->reviews_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500">В избранном</dt>
                        <dd class="font-medium tabular-nums">{{ $user->favorite_products_count }} товаров</dd>
                    </div>
                </dl>
            </div>

            <p class="rounded-lg border border-neutral-200 bg-neutral-50 p-4 text-xs leading-relaxed text-neutral-600">
                Доступны только данные, нужные для поддержки и заказов. Адреса доставки — в карточках заказов, не в профиле.
            </p>
        </div>
    </div>
@endsection
