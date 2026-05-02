@extends('layouts.app')

@php
    $sizes = $product->sizesList();
@endphp

@section('content')
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-16">
        <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100 lg:aspect-auto lg:min-h-[640px]">
            <img src="{{ $product->image ?: 'https://picsum.photos/1000/1333?random='.$product->id }}" alt="" class="absolute inset-0 h-full w-full object-cover">
            <button type="button" class="favorite-btn absolute bottom-4 right-4 z-10 flex h-11 w-11 items-center justify-center rounded-full border border-neutral-200 bg-white/90 shadow-sm backdrop-blur-sm" data-id="{{ $product->id }}">
                <svg class="heart-icon h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            </button>
        </div>

        <div class="flex flex-col justify-center px-1 lg:max-w-md">
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-neutral-500">ДЯБ</p>
            <h1 class="mt-3 text-2xl font-normal uppercase leading-tight tracking-wide text-black md:text-3xl">{{ $product->name }}</h1>
            <p class="mt-4 text-2xl font-bold text-black">{{ number_format($product->price, 0, '.', ' ') }} ₽</p>
            @if($product->description)
                <p class="mt-6 text-sm leading-relaxed text-neutral-600">{{ $product->description }}</p>
            @endif

            @auth
                <form id="add-cart-form" method="POST" action="{{ route('cart.add', $product) }}" class="mt-10 space-y-6">
                    @csrf
                    <div>
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-neutral-900">Размер <span class="text-red-600">*</span></p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($sizes as $sz)
                                <label class="cursor-pointer">
                                    <input type="radio" name="size" value="{{ $sz }}" class="peer sr-only" required>
                                    <span class="flex min-w-[3rem] items-center justify-center border border-neutral-900 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide peer-checked:bg-black peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-neutral-400">{{ $sz }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="text-xs uppercase tracking-wider text-neutral-600">Кол-во</label>
                        <input type="number" name="quantity" value="1" min="1" max="20" class="w-20 border border-neutral-300 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" id="add-cart-btn" disabled class="w-full bg-black py-4 text-xs font-semibold uppercase tracking-[0.2em] text-white transition enabled:hover:bg-neutral-800 disabled:cursor-not-allowed disabled:bg-neutral-300">
                        Выберите размер
                    </button>
                </form>
            @else
                <p class="mt-10 text-sm text-neutral-600"><a href="{{ route('otp.form') }}" class="underline">Войдите</a>, чтобы добавить в корзину и оформить заказ.</p>
            @endauth

            <p class="delivery-hint mt-8 text-xs text-neutral-500"></p>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const FAV_KEY = 'dyab-favorites';
            const readFav = () => new Set(JSON.parse(localStorage.getItem(FAV_KEY) || '[]'));
            const writeFav = (s) => localStorage.setItem(FAV_KEY, JSON.stringify([...s]));
            const btn = document.querySelector('.favorite-btn');
            const svg = btn?.querySelector('.heart-icon');
            const id = Number(btn?.dataset.id);
            function sync() {
                const fav = readFav();
                if (fav.has(id)) {
                    svg?.setAttribute('fill', 'currentColor');
                    btn?.classList.add('text-red-600');
                } else {
                    svg?.setAttribute('fill', 'none');
                    btn?.classList.remove('text-red-600');
                }
            }
            sync();
            btn?.addEventListener('click', () => {
                const fav = readFav();
                if (fav.has(id)) fav.delete(id); else fav.add(id);
                writeFav(fav);
                sync();
                window.dyabToast?.(fav.has(id) ? 'В избранном' : 'Убрано из избранного');
            });

            const form = document.getElementById('add-cart-form');
            const submitBtn = document.getElementById('add-cart-btn');
            form?.querySelectorAll('input[name="size"]').forEach((r) => {
                r.addEventListener('change', () => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'В корзину';
                    }
                });
            });

            const el = document.querySelector('.delivery-hint');
            function tick() {
                const now = new Date();
                const cutoff = new Date();
                cutoff.setHours(16, 0, 0, 0);
                const diff = cutoff - now;
                const targetDay = diff > 0 ? 'завтра' : 'послезавтра';
                const remain = diff > 0 ? diff : (24 * 60 * 60 * 1000 + diff);
                const h = String(Math.floor(remain / 3600000)).padStart(2, '0');
                const m = String(Math.floor((remain % 3600000) / 60000)).padStart(2, '0');
                if (el) el.textContent = `Закажи за ${h}:${m} — получи ${targetDay}`;
            }
            tick();
            setInterval(tick, 30000);
        })();
    </script>
    @endpush
@endsection
