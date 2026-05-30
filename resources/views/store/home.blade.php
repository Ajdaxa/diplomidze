@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    @php $favoriteLookup = array_flip(array_map('intval', $favoriteIds ?? [])); @endphp

    <section class="relative mb-12 overflow-hidden rounded-2xl border border-neutral-200 bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 text-white sm:mb-16 sm:rounded-3xl">
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
                <a href="#contacts" class="inline-flex min-h-11 items-center justify-center border border-white/40 bg-white/10 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white backdrop-blur-sm transition hover:bg-white/20">Контакты</a>
            </div>
        </div>
    </section>

    @if($hitProducts->isNotEmpty())
        <section class="mb-14 sm:mb-16" aria-label="Хиты">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500">Хиты</h2>
                    <p class="mt-1 text-lg font-light text-neutral-900 sm:text-xl">Актуальная подборка</p>
                    <p class="mt-1 max-w-md text-sm text-neutral-500">Подборка обновляется автоматически: новинки, лимитированные модели и недавно обновлённые позиции.</p>
                </div>
                <a href="{{ route('catalog') }}" class="text-xs font-semibold uppercase tracking-wider text-neutral-600 underline decoration-neutral-300 underline-offset-4 hover:text-black">Весь каталог</a>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
                @foreach($hitProducts as $product)
                    <article class="group overflow-hidden rounded-xl border border-neutral-200 bg-white transition hover:border-neutral-900">
                        <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-[3/4] overflow-hidden bg-neutral-100">
                            <img src="{{ $product->image ?: 'https://picsum.photos/600/800?random='.$product->id }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                            @auth
                                <button type="button" class="favorite-btn absolute bottom-3 right-3 z-10 flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 bg-white/90 text-neutral-800 shadow-sm backdrop-blur-sm hover:bg-white {{ isset($favoriteLookup[$product->id]) ? 'text-red-600' : '' }}" data-id="{{ $product->id }}" data-active="{{ isset($favoriteLookup[$product->id]) ? '1' : '0' }}" aria-label="Избранное">
                                    <svg class="heart-icon h-5 w-5" fill="{{ isset($favoriteLookup[$product->id]) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                </button>
                            @else
                                <a href="{{ route('otp.form', ['redirect' => request()->fullUrl()]) }}" class="absolute bottom-3 right-3 z-10 flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 bg-white/90 text-neutral-800 shadow-sm backdrop-blur-sm hover:bg-white" aria-label="Войдите для избранного">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                </a>
                            @endauth
                            <x-product-badge :product="$product" />
                        </a>
                        <div class="p-3 sm:p-4">
                            <h3 class="text-[11px] font-medium uppercase leading-snug tracking-wide text-neutral-900 sm:text-xs">
                                <a href="{{ route('products.show', $product->slug) }}" class="hover:underline">{{ $product->name }}</a>
                            </h3>
                            <x-product-price :product="$product" class="mt-1" />
                        </div>
                    </article>
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
                <a href="mailto:{{ config('mail.from.address', 'support@dyab.ru') }}" class="mt-2 inline-block text-sm font-medium text-neutral-900 underline decoration-neutral-300 underline-offset-2 hover:decoration-black">{{ config('mail.from.address', 'support@dyab.ru') }}</a>
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
