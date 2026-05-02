<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Дəб</title>
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
<body class="bg-white text-neutral-900 antialiased"
      data-flash-status='@json(session("status"))'
      data-flash-status-type='@json(session("status_type", "success"))'
      data-flash-error='@json($errors->first())'>
<div class="min-h-screen">
    <header class="sticky top-0 z-50 border-b border-neutral-200 bg-white/75 backdrop-blur-[12px]">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 md:px-6">
            <a href="{{ route('home') }}" class="text-lg font-semibold tracking-[0.2em]">Дəб</a>
            <nav class="flex items-center gap-4 md:gap-6 text-xs font-medium uppercase tracking-wider">
                <a href="{{ route('home') }}" class="text-neutral-600 hover:text-black">Витрина</a>
                <a href="{{ route('favorites.index') }}" class="text-neutral-600 hover:text-black">Избранное</a>
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
        @yield('content')
    </main>
</div>
<div id="toast" class="pointer-events-none fixed bottom-6 right-6 z-50 hidden max-w-sm rounded-xl border px-4 py-3 text-sm shadow-xl transition"></div>
<button id="scroll-top-btn" type="button" class="fixed bottom-6 left-6 z-40 hidden rounded-full border border-neutral-300 bg-white p-2 text-neutral-700 shadow hover:bg-neutral-50" aria-label="Наверх">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 15l7-7 7 7"/></svg>
</button>
<script>
    window.dyabNotify = (text, type = 'success') => {
        const el = document.getElementById('toast');
        if (!el) return;
        el.textContent = text;
        el.className = 'pointer-events-none fixed bottom-6 right-6 z-50 max-w-sm rounded-xl border px-4 py-3 text-sm shadow-xl transition';
        const palette = {
            success: 'border-emerald-200 bg-emerald-50 text-emerald-900',
            info: 'border-sky-200 bg-sky-50 text-sky-900',
            warn: 'border-amber-200 bg-amber-50 text-amber-900',
            error: 'border-rose-200 bg-rose-50 text-rose-900',
        };
        el.classList.add(...(palette[type] || palette.success).split(' '));
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 2400);
    };
    window.dyabToast = window.dyabNotify;

    const body = document.body;
    const status = body?.dataset.flashStatus ? JSON.parse(body.dataset.flashStatus) : null;
    const statusType = body?.dataset.flashStatusType ? JSON.parse(body.dataset.flashStatusType) : 'success';
    const firstError = body?.dataset.flashError ? JSON.parse(body.dataset.flashError) : null;
    if (status) window.dyabNotify(status, statusType || 'success');
    if (firstError) window.dyabNotify(firstError, 'error');

    const topBtn = document.getElementById('scroll-top-btn');
    window.addEventListener('scroll', () => topBtn?.classList.toggle('hidden', window.scrollY < 260));
    topBtn?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>
@stack('scripts')
</body>
</html>
