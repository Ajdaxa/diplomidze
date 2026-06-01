<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ-панель') — Дəб</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --admin-pad: clamp(1rem, 3vw, 2rem); }
        @media (max-width: 1023px) {
            body.admin-shell { overflow-x: hidden; }
        }
    </style>
</head>
<body class="admin-shell min-h-screen min-h-[100dvh] bg-neutral-100 text-neutral-900"
      data-flash-status='@json(session("status"))'
      data-flash-status-type='@json(session("status_type", "success"))'
      data-flash-error='@json($errors->first())'>
<div class="flex min-h-screen min-h-[100dvh]">
    <div id="admin-sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-neutral-950/40 backdrop-blur-[1px] lg:hidden" aria-hidden="true"></div>
    <aside id="admin-sidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-[min(18rem,88vw)] -translate-x-full flex-col border-r border-neutral-200 bg-white px-4 py-6 transition-transform duration-200 ease-out lg:static lg:z-auto lg:w-64 lg:translate-x-0 lg:shrink-0"
           aria-label="Админ-меню">
        <a href="{{ route('admin.hub') }}" class="block text-lg font-semibold tracking-widest">Дəб</a>
        <p class="mt-1 text-xs uppercase tracking-wider text-neutral-500">Админ-панель</p>
        <nav class="mt-8 flex flex-col gap-1 text-sm">
            <a href="{{ route('admin.hub') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Главная</a>
            <a href="{{ route('admin.dashboard') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Аналитика</a>
            <a href="{{ route('admin.categories.index') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Категории</a>
            <a href="{{ route('admin.products.index') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Товары</a>
            <a href="{{ route('admin.sales.index') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Скидки на товары</a>
            <a href="{{ route('admin.products.create') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Добавить товар</a>
            <a href="{{ route('admin.orders.index') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Заказы</a>
            <a href="{{ route('admin.couriers.index') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Курьеры</a>
            <a href="{{ route('admin.promocodes.index') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Промокоды</a>
            <a href="{{ route('admin.reviews.index') }}" class="min-h-10 rounded-lg px-3 py-2 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">Отзывы</a>
        </nav>
        <div class="mt-auto border-t border-neutral-200 pt-4">
            <a href="{{ route('home') }}" class="text-xs text-neutral-500 hover:text-black">На витрину</a>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">@csrf<button type="submit" class="text-xs text-neutral-500 hover:text-black">Выход</button></form>
        </div>
    </aside>
    <div class="flex min-h-screen min-h-[100dvh] flex-1 flex-col lg:min-w-0">
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-neutral-200 bg-white/95 px-4 py-3 backdrop-blur-sm sm:px-6 lg:px-8 lg:py-4">
            <button type="button"
                    id="admin-menu-open"
                    class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border border-neutral-200 bg-white text-neutral-900 lg:hidden"
                    aria-expanded="false"
                    aria-controls="admin-sidebar"
                    aria-label="Открыть меню">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="min-w-0 flex-1 text-lg font-medium leading-tight sm:text-xl">@yield('heading', 'Панель управления')</h1>
        </header>
        <main class="flex-1 p-[var(--admin-pad)]">
            @yield('content')
        </main>
    </div>
</div>
<div id="admin-toast" class="pointer-events-none fixed inset-x-4 bottom-[max(1rem,env(safe-area-inset-bottom))] z-50 hidden rounded-xl border px-4 py-3 text-sm shadow-xl transition sm:inset-x-auto sm:right-6 sm:max-w-sm"></div>
<script>
    window.dyabAdminNotify = (text, type = 'success') => {
        const el = document.getElementById('admin-toast');
        if (!el) return;
        el.textContent = text;
        el.className = 'pointer-events-none fixed inset-x-4 bottom-[max(1rem,env(safe-area-inset-bottom))] z-50 rounded-xl border px-4 py-3 text-sm shadow-xl transition sm:inset-x-auto sm:right-6 sm:max-w-sm';
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
    const body = document.body;
    const status = body?.dataset.flashStatus ? JSON.parse(body.dataset.flashStatus) : null;
    const statusType = body?.dataset.flashStatusType ? JSON.parse(body.dataset.flashStatusType) : 'success';
    const firstError = body?.dataset.flashError ? JSON.parse(body.dataset.flashError) : null;
    if (status) window.dyabAdminNotify(status, statusType || 'success');
    if (firstError) window.dyabAdminNotify(firstError, 'error');

    (function () {
        const sidebar = document.getElementById('admin-sidebar');
        const backdrop = document.getElementById('admin-sidebar-backdrop');
        const openBtn = document.getElementById('admin-menu-open');
        if (!sidebar || !openBtn) return;

        const setOpen = (open) => {
            sidebar.classList.toggle('-translate-x-full', !open);
            backdrop?.classList.toggle('hidden', !open);
            openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            document.body.style.overflow = open ? 'hidden' : '';
        };

        openBtn.addEventListener('click', () => setOpen(sidebar.classList.contains('-translate-x-full')));
        backdrop?.addEventListener('click', () => setOpen(false));
        sidebar.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 1023px)').matches) setOpen(false);
        }));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') setOpen(false);
        });
    })();
</script>
@stack('scripts')
</body>
</html>
