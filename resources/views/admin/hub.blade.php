@extends('layouts.admin')

@section('title', 'Центр управления')
@section('heading', 'Центр управления')

@section('content')
    <p class="mb-8 max-w-xl text-sm text-neutral-600">Выберите раздел. Все инструменты собраны здесь — без лишних ссылок в шапке витрины.</p>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('admin.dashboard') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Аналитика</h2>
            <p class="mt-2 text-xs text-neutral-500">Графики продаж, промокоды, курьеры</p>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Категории</h2>
            <p class="mt-2 text-xs text-neutral-500">Разделы витрины</p>
        </a>
        <a href="{{ route('admin.products.index') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Каталог</h2>
            <p class="mt-2 text-xs text-neutral-500">Список товаров, редактирование</p>
        </a>
        <a href="{{ route('admin.sales.index') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Скидки на товары</h2>
            <p class="mt-2 text-xs text-neutral-500">Выбор позиций и размер скидки</p>
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Отзывы</h2>
            <p class="mt-2 text-xs text-neutral-500">
                Модерация
                @if($pendingReviews > 0)
                    <span class="ml-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-900">{{ $pendingReviews }} новых</span>
                @endif
            </p>
        </a>
        <a href="{{ route('admin.products.create') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Новый товар</h2>
            <p class="mt-2 text-xs text-neutral-500">Отдельная форма добавления</p>
        </a>
        <a href="{{ route('admin.users.index') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Клиенты</h2>
            <p class="mt-2 text-xs text-neutral-500">Контакты, баллы, история заказов</p>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Заказы</h2>
            <p class="mt-2 text-xs text-neutral-500">
                Статусы и курьеры
                @if($paidOrders > 0)
                    <span class="ml-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-900">{{ $paidOrders }} новых</span>
                @endif
            </p>
        </a>
        <a href="{{ route('admin.couriers.index') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Курьеры</h2>
            <p class="mt-2 text-xs text-neutral-500">Создание и доступ</p>
        </a>
        <a href="{{ route('admin.promocodes.index') }}" class="group rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition hover:border-black">
            <h2 class="text-sm font-semibold uppercase tracking-wider">Промокоды</h2>
            <p class="mt-2 text-xs text-neutral-500">Создание и управление кодами</p>
        </a>
    </div>
@endsection
