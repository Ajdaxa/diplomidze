@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    @php $favoriteLookup = array_flip(array_map('intval', $favoriteIds ?? [])); @endphp

    <section class="relative mb-10 overflow-hidden rounded-2xl border border-neutral-200 bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 text-white sm:mb-12 sm:rounded-3xl">
        <div class="pointer-events-none absolute inset-0 opacity-30">
            @if($hitProducts->isNotEmpty() && ($heroImg = $hitProducts->first()->image))
                <img src="{{ $heroImg }}" alt="" class="h-full w-full object-cover">
            @endif
        </div>
        <div class="relative px-6 py-14 sm:px-10 sm:py-20 lg:px-14 lg:py-24">
            <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-white/70 sm:text-xs">Новая коллекция</p>
            <h1 class="mt-4 max-w-xl text-[clamp(1.75rem,5vw,3rem)] font-light leading-tight tracking-tight">Соберите образ без лишнего шума</h1>
            <p class="mt-4 max-w-md text-sm leading-relaxed text-white/85 sm:text-base">Минималистичные формы, честные материалы и доставка до двери. Загляните в хиты недели или полный каталог.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('catalog') }}" class="inline-flex min-h-11 items-center justify-center border border-white bg-white px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-900 transition hover:bg-neutral-100">Каталог</a>
                <a href="#sale" class="inline-flex min-h-11 items-center justify-center border border-white/40 bg-white/10 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white backdrop-blur-sm transition hover:bg-white/20">Скидки</a>
            </div>
        </div>
    </section>

    <section class="mb-12 grid gap-4 sm:mb-14 sm:grid-cols-3" aria-label="Преимущества">
        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-neutral-500">Доставка</p>
            <p class="mt-2 text-sm font-medium text-neutral-900">Курьер до двери</p>
            <p class="mt-1 text-xs leading-relaxed text-neutral-600">Отслеживайте статус заказа в профиле и получайте уведомления в Telegram.</p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-neutral-500">Баллы</p>
            <p class="mt-2 text-sm font-medium text-neutral-900">5% с каждой покупки</p>
            <p class="mt-1 text-xs leading-relaxed text-neutral-600">Списывайте до 30% суммы заказа при оформлении — 1 балл равен 1 ₽.</p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-neutral-500">Оплата</p>
            <p class="mt-2 text-sm font-medium text-neutral-900">YooMoney онлайн</p>
            <p class="mt-1 text-xs leading-relaxed text-neutral-600">Безопасная оплата картой сразу после оформления заказа.</p>
        </div>
    </section>

    @if($saleProducts->isNotEmpty())
        <section id="sale" class="mb-14 scroll-mt-24 sm:mb-16" aria-label="Скидки">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500">Скидки</h2>
                    <p class="mt-1 text-lg font-light text-neutral-900">Выгодные позиции</p>
                    <p class="mt-1 max-w-md text-sm text-neutral-500">Актуальные предложения с уменьшенной ценой.</p>
                </div>
                <a href="{{ route('catalog') }}" class="text-xs font-semibold uppercase tracking-wider text-neutral-600 underline decoration-neutral-300 underline-offset-4 hover:text-black">Смотреть все</a>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                @foreach($saleProducts as $product)
                    @include('store.partials.product-card', ['product' => $product, 'favoriteLookup' => $favoriteLookup])
                @endforeach
            </div>
        </section>
    @endif

    @if($hitProducts->isNotEmpty())
        <section class="mb-14 sm:mb-16" aria-label="Хиты">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500">Хиты</h2>
                    <p class="mt-1 text-lg font-light text-neutral-900">Актуальная подборка</p>
                    <p class="mt-1 max-w-md text-sm text-neutral-500">Новинки, лимитированные модели и недавно обновлённые позиции.</p>
                </div>
                <a href="{{ route('catalog') }}" class="text-xs font-semibold uppercase tracking-wider text-neutral-600 underline decoration-neutral-300 underline-offset-4 hover:text-black">Весь каталог</a>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
                @foreach($hitProducts as $product)
                    @include('store.partials.product-card', ['product' => $product, 'favoriteLookup' => $favoriteLookup])
                @endforeach
            </div>
        </section>
    @endif

    <section id="contacts" class="scroll-mt-24 rounded-2xl border border-neutral-200 bg-neutral-50 p-6 sm:p-8 lg:p-10">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500">Контакты</h2>
        <p class="mt-2 max-w-2xl text-sm text-neutral-600 sm:text-[0.9375rem]">Напишите нам — подскажем по размеру, доставке или заказу. Ответим в рабочие часы.</p>
        <div class="mt-8 grid gap-6 sm:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Почта</p>
                <a href="mailto:{{ config('site.support_email') }}" class="mt-2 inline-block text-sm font-medium text-neutral-900 underline decoration-neutral-300 underline-offset-2 hover:decoration-black">{{ config('site.support_email') }}</a>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Время работы</p>
                <p class="mt-2 text-sm font-medium text-neutral-900">Пн–Вс, 10:00–20:00</p>
                <p class="mt-1 text-xs text-neutral-500">МСК</p>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Доставка</p>
                <p class="mt-2 text-sm font-medium text-neutral-900">Курьер и транспортные компании</p>
                <p class="mt-1 text-xs text-neutral-500">Оплата онлайн (YooMoney)</p>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.querySelectorAll('.favorite-btn').forEach((btn) => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = Number(btn.dataset.id);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/favorites/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                if (!res.ok) {
                    window.dyabNotify?.('Нужно войти в аккаунт.', 'warn');
                    window.location.href = `{{ route('otp.form') }}?redirect=${encodeURIComponent(window.location.href)}`;
                    return;
                }
                const data = await res.json();
                const added = data.status === 'added';
                const svg = btn.querySelector('.heart-icon');
                svg?.setAttribute('fill', added ? 'currentColor' : 'none');
                btn.classList.toggle('text-red-600', added);
                btn.dataset.active = added ? '1' : '0';
                window.dyabNotify?.(data.message || (added ? 'Добавлено в избранное.' : 'Убрано из избранного.'), added ? 'success' : 'info');
            });
        });
    </script>
    @endpush
@endsection
