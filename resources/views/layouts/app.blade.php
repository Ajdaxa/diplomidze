<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ДЯБ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f5f5f4; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(#292524, #57534e); border-radius: 999px; }
    </style>
</head>
<body class="bg-stone-50 text-stone-800">
<div class="min-h-screen">
    <header class="sticky top-0 z-40 border-b border-stone-200/70 bg-white/80 backdrop-blur-[12px]">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="text-xl font-semibold tracking-wider">ДЯБ</a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('home') }}" class="hover:text-black">Витрина</a>
                @auth
                    <a href="{{ route('cart.index') }}" class="hover:text-black">Корзина</a>
                    <a href="{{ route('checkout.create') }}" class="hover:text-black">Оформление</a>
                    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                        <a href="{{ route('admin.products.index') }}" class="hover:text-black">Товары</a>
                        <a href="{{ route('admin.orders.index') }}" class="hover:text-black">Заказы</a>
                        <a href="{{ route('admin.couriers.index') }}" class="hover:text-black">Курьеры</a>
                        <a href="{{ route('admin.promocodes.index') }}" class="hover:text-black">Промокоды</a>
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-black">Админка</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">@csrf<button>Выход</button></form>
                @else
                    <a href="{{ route('otp.form') }}" class="hover:text-black">Войти</a>
                @endauth
            </nav>
        </div>
    </header>
    <main class="mx-auto max-w-6xl px-6 py-8">
        @if (session('status'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif
        @yield('content')
    </main>
</div>
<div id="toast" class="pointer-events-none fixed bottom-6 right-6 hidden rounded-lg bg-stone-900 px-4 py-3 text-sm text-white shadow-lg"></div>
<script>
    window.dyabToast = (text) => {
        const el = document.getElementById('toast');
        if (!el) return;
        el.textContent = text;
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 1800);
    };
</script>
</body>
</html>
