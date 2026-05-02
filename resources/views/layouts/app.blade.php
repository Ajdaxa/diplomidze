<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ДЯБ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0"></script>
    <style>
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #fafafa; }
        ::-webkit-scrollbar-thumb { background: #171717; border-radius: 3px; }
    </style>
</head>
@php
    $cartCount = collect(session('cart', []))->sum();
@endphp
<body class="bg-white text-neutral-900 antialiased">
<div class="min-h-screen">
    <header class="sticky top-0 z-50 border-b border-neutral-200 bg-white/75 backdrop-blur-[12px]">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 md:px-6">
            <a href="{{ route('home') }}" class="text-lg font-semibold tracking-[0.2em]">ДЯБ</a>
            <nav class="flex items-center gap-4 md:gap-6 text-xs font-medium uppercase tracking-wider">
                <a href="{{ route('home') }}" class="text-neutral-600 hover:text-black">Витрина</a>
                <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-1.5 text-neutral-600 hover:text-black">
                    Корзина
                    @if($cartCount > 0)
                        <span class="rounded-full bg-black px-2 py-0.5 text-[10px] text-white">{{ $cartCount }}</span>
                    @endif
                </a>
                @auth
                    @if(auth()->user()->hasRole('client'))
                        <a href="{{ route('profile.show') }}" class="text-neutral-600 hover:text-black">Профиль</a>
                    @endif
                    @if(auth()->user()->hasRole('courier'))
                        <a href="{{ route('courier.orders.index') }}" class="text-neutral-600 hover:text-black">Доставка</a>
                    @endif
                    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                        <a href="{{ route('admin.hub') }}" class="text-neutral-900 hover:underline">Админ-панель</a>
                    @endif
                    @if(auth()->user()->hasRole('client') || ! auth()->user()->hasAnyRole(['admin', 'manager', 'courier']))
                        <a href="{{ route('checkout.create') }}" class="hidden text-neutral-600 hover:text-black sm:inline">Оформить</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">@csrf<button type="submit" class="text-neutral-500 hover:text-black">Выход</button></form>
                @else
                    <a href="{{ route('otp.form') }}" class="text-neutral-600 hover:text-black">Войти</a>
                @endauth
            </nav>
        </div>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-8 md:px-6">
        @if (session('status'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
</div>
<div id="toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden max-w-sm rounded border border-neutral-900 bg-neutral-900 px-4 py-3 text-sm text-white shadow-lg"></div>
<script>
    window.dyabToast = (text) => {
        const el = document.getElementById('toast');
        if (!el) return;
        el.textContent = text;
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 2000);
    };
</script>
@stack('scripts')
</body>
</html>
