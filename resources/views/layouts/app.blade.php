<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="@yield('meta_description', 'Дəб — минималистичный интернет-магазин одежды и аксессуаров. Каталог, доставка, оплата онлайн.')">
    <title>@hasSection('title')@yield('title') — Дəб@elseДəб — интернет-магазин@endif</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'Segoe UI', 'sans-serif'] } } } };
    </script>
    <style>
        :root {
            --container: 80rem;
            --pad-x: clamp(1rem, 4vw, 1.75rem);
            --pad-y-section: clamp(1.5rem, 4vw, 2.5rem);
            --text-lede: clamp(0.8125rem, 0.8rem + 0.35vw, 0.9375rem);
        }
        html { scroll-behavior: smooth; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        body { overflow-x: hidden; font-family: Inter, system-ui, 'Segoe UI', sans-serif; }
        :focus-visible { outline: 2px solid #171717; outline-offset: 2px; }
        .container-app {
            width: 100%;
            max-width: var(--container);
            margin-left: auto;
            margin-right: auto;
            padding-left: var(--pad-x);
            padding-right: var(--pad-x);
        }
        .tabs-scroll {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #a3a3a3 #f5f5f5;
        }
        .tabs-scroll::-webkit-scrollbar { height: 4px; }
        .tabs-scroll::-webkit-scrollbar-thumb { background: #737373; border-radius: 4px; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #fafafa; }
        ::-webkit-scrollbar-thumb { background: #171717; border-radius: 3px; }
    </style>
</head>
@php
    $cartCount = collect(session('cart', []))->sum();
@endphp
<body class="min-h-screen min-h-[100dvh] bg-white text-neutral-900 antialiased [text-rendering:optimizeLegibility]"
      data-flash-status='@json(session("status"))'
      data-flash-status-type='@json(session("status_type", "success"))'
      data-flash-error='@json($errors->first())'>
<div class="flex min-h-screen min-h-[100dvh] flex-col">
    <header class="sticky top-0 z-50 border-b border-neutral-200 bg-white/80 backdrop-blur-md supports-[backdrop-filter]:bg-white/70">
        <div class="container-app flex min-h-14 items-center justify-between gap-3 py-2 sm:min-h-16 sm:py-3">
            <a href="{{ route('home') }}" class="shrink-0 text-base font-semibold tracking-[0.18em] text-neutral-900 sm:text-lg">Дəб</a>
            <div class="ml-auto flex shrink-0 items-center justify-end gap-2 sm:gap-3">
                <nav class="hidden items-center justify-end gap-x-4 lg:flex xl:gap-x-6" aria-label="Основная навигация">
                    @include('partials.site-nav-links', ['variant' => 'desktop', 'cartCount' => $cartCount])
                </nav>
                <button type="button"
                        id="mobile-menu-open"
                        class="inline-flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-lg border border-neutral-200 bg-white text-neutral-900 shadow-sm lg:hidden focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900"
                        aria-expanded="false"
                        aria-controls="mobile-nav-drawer"
                        aria-label="Открыть меню">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </header>

    <div id="mobile-nav-drawer"
         class="fixed inset-0 z-[60] hidden lg:hidden"
         role="dialog"
         aria-modal="true"
         aria-label="Меню сайта"
         aria-hidden="true">
        <button type="button"
                id="mobile-menu-backdrop"
                class="absolute inset-0 bg-neutral-950/40 backdrop-blur-[2px]"
                aria-label="Закрыть меню"></button>
        <div class="absolute right-0 top-0 flex h-full w-[min(100%,20rem)] flex-col border-l border-neutral-200 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-neutral-100 px-4 py-3">
                <span class="text-xs font-semibold uppercase tracking-widest text-neutral-500">Меню</span>
                <button type="button"
                        id="mobile-menu-close"
                        class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg text-neutral-600 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900"
                        aria-label="Закрыть">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex flex-1 flex-col gap-1 overflow-y-auto p-4" aria-label="Мобильная навигация">
                @include('partials.site-nav-links', ['variant' => 'mobile', 'cartCount' => $cartCount])
            </nav>
        </div>
    </div>

    <main class="container-app flex-1 py-[var(--pad-y-section)]">
        @yield('content')
    </main>
    @include('partials.site-footer')
</div>
<div id="toast" class="pointer-events-none fixed inset-x-4 bottom-[max(1rem,env(safe-area-inset-bottom))] z-50 hidden rounded-xl border px-4 py-3 text-sm shadow-xl transition sm:inset-x-auto sm:right-6 sm:max-w-sm"></div>
<button id="scroll-top-btn" type="button" class="fixed bottom-[max(5.5rem,env(safe-area-inset-bottom)+4.5rem)] left-4 z-40 hidden rounded-full border border-neutral-300 bg-white p-2.5 text-neutral-700 shadow-md hover:bg-neutral-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 sm:bottom-6 sm:left-6" aria-label="Наверх">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 15l7-7 7 7"/></svg>
</button>
<script>
    window.dyabNotify = (text, type = 'success') => {
        const el = document.getElementById('toast');
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

    (function () {
        const drawer = document.getElementById('mobile-nav-drawer');
        const openBtn = document.getElementById('mobile-menu-open');
        const closeBtn = document.getElementById('mobile-menu-close');
        const backdrop = document.getElementById('mobile-menu-backdrop');
        if (!drawer || !openBtn) return;

        const setOpen = (open) => {
            drawer.classList.toggle('hidden', !open);
            drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
            openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            document.body.style.overflow = open ? 'hidden' : '';
            if (open) closeBtn?.focus({ preventScroll: true });
        };

        openBtn.addEventListener('click', () => setOpen(true));
        closeBtn?.addEventListener('click', () => setOpen(false));
        backdrop?.addEventListener('click', () => setOpen(false));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !drawer.classList.contains('hidden')) setOpen(false);
        });
    })();
</script>
@stack('scripts')
</body>
</html>
