<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ-панель') — ДЯБ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen bg-neutral-100 text-neutral-900">
<div class="flex min-h-screen">
    <aside class="w-64 shrink-0 border-r border-neutral-200 bg-white px-4 py-6">
        <a href="{{ route('admin.hub') }}" class="block text-lg font-semibold tracking-widest">ДЯБ</a>
        <p class="mt-1 text-xs uppercase tracking-wider text-neutral-500">Админ-панель</p>
        <nav class="mt-8 flex flex-col gap-1 text-sm">
            <a href="{{ route('admin.hub') }}" class="rounded px-3 py-2 hover:bg-neutral-100">Главная</a>
            <a href="{{ route('admin.dashboard') }}" class="rounded px-3 py-2 hover:bg-neutral-100">Аналитика</a>
            <a href="{{ route('admin.products.index') }}" class="rounded px-3 py-2 hover:bg-neutral-100">Товары</a>
            <a href="{{ route('admin.products.create') }}" class="rounded px-3 py-2 hover:bg-neutral-100">Добавить товар</a>
            <a href="{{ route('admin.orders.index') }}" class="rounded px-3 py-2 hover:bg-neutral-100">Заказы</a>
            <a href="{{ route('admin.couriers.index') }}" class="rounded px-3 py-2 hover:bg-neutral-100">Курьеры</a>
            <a href="{{ route('admin.promocodes.index') }}" class="rounded px-3 py-2 hover:bg-neutral-100">Промокоды</a>
        </nav>
        <div class="mt-8 border-t border-neutral-200 pt-4">
            <a href="{{ route('home') }}" class="text-xs text-neutral-500 hover:text-black">На витрину</a>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">@csrf<button type="submit" class="text-xs text-neutral-500 hover:text-black">Выход</button></form>
        </div>
    </aside>
    <div class="flex min-h-screen flex-1 flex-col">
        <header class="border-b border-neutral-200 bg-white px-8 py-4">
            <h1 class="text-xl font-medium">@yield('heading', 'Панель управления')</h1>
        </header>
        <main class="flex-1 p-8">
            @if (session('status'))
                <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
