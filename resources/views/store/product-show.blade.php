@extends('layouts.app')

@php
$sizes = $product->sizesList();
@endphp

@section('content')
<div class="grid grid-cols-1 gap-8 sm:gap-10 lg:grid-cols-2 lg:items-start lg:gap-12 xl:gap-16">
    <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100 sm:aspect-[3/4] lg:sticky lg:top-20 lg:aspect-auto lg:min-h-[min(85vh,40rem)] xl:min-h-[640px]">
        <img src="{{ $product->image ?: 'https://picsum.photos/1000/1333?random='.$product->id }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        @if($product->is_new_collection)
            <span class="absolute left-4 top-4 bg-black px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-white">New</span>
        @elseif($product->is_limited_edition)
            <span class="absolute left-4 top-4 bg-neutral-800 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-white">Limited</span>
        @endif
        @auth
        <button type="button" class="favorite-btn absolute bottom-4 right-4 z-10 flex h-11 w-11 min-h-[2.75rem] min-w-[2.75rem] items-center justify-center rounded-full border border-neutral-200 bg-white/90 shadow-sm backdrop-blur-sm {{ $isFavorite ? 'text-red-600' : '' }}" data-id="{{ $product->id }}" data-active="{{ $isFavorite ? '1' : '0' }}">
            <svg class="heart-icon h-6 w-6" fill="{{ $isFavorite ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
        </button>
        @else
        <a href="{{ route('otp.form', ['redirect' => request()->fullUrl()]) }}" class="absolute bottom-4 right-4 z-10 flex h-11 w-11 min-h-[2.75rem] min-w-[2.75rem] items-center justify-center rounded-full border border-neutral-200 bg-white/90 shadow-sm backdrop-blur-sm">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
        </a>
        @endauth
    </div>

    <div class="flex min-w-0 flex-col justify-center px-0 sm:px-1 lg:max-w-xl">
        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-neutral-500 sm:text-[11px]">Дəб</p>
        <h1 class="mt-3 text-[clamp(1.25rem,4vw,1.875rem)] font-normal uppercase leading-tight tracking-wide text-black">{{ $product->name }}</h1>
        <p class="mt-4 text-[clamp(1.375rem,3.5vw,1.75rem)] font-bold text-black">{{ number_format($product->price, 0, '.', ' ') }} ₽</p>
        @if($product->stock < 1)
            <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-rose-700">Нет в наличии</p>
            @elseif($product->stock < 4)
                <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-amber-700">Осталось: {{ $product->stock }}</p>
                @endif
                @if($product->description)
                <p class="mt-6 max-w-prose text-sm leading-relaxed text-neutral-600 sm:text-[15px]">{{ $product->description }}</p>
                @endif
                @if($product->composition)
                    <div class="mt-6 max-w-prose">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Состав</p>
                        <p class="mt-2 text-sm leading-relaxed text-neutral-700 sm:text-[15px]">{{ $product->composition }}</p>
                    </div>
                @endif



                @auth
                <form id="add-cart-form" method="POST" action="{{ route('cart.add', $product) }}" class="mt-8 space-y-6 sm:mt-10">
                    @csrf
                    <div>
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-neutral-900">Размер <span class="text-red-600">*</span></p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($sizes as $sz)
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="{{ $sz }}" class="peer sr-only" required>
                                <span class="flex min-h-11 min-w-[3rem] items-center justify-center border border-neutral-900 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide peer-checked:bg-black peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-neutral-400">{{ $sz }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="text-xs uppercase tracking-wider text-neutral-600">Кол-во</label>
                        <input type="number" name="quantity" value="1" min="1" max="20" class="w-20 border border-neutral-300 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" id="add-cart-btn" data-sold-out="{{ $product->stock < 1 ? '1' : '0' }}" @disabled($product->stock < 1) class="w-full min-h-12 bg-black py-3.5 text-xs font-semibold uppercase tracking-[0.2em] text-white transition enabled:hover:bg-neutral-800 disabled:cursor-not-allowed disabled:bg-neutral-300 sm:min-h-[3.25rem] sm:py-4">
                            {{ $product->stock < 1 ? 'Нет в наличии' : 'Выберите размер' }}
                    </button>
                </form>
                @else
                <p class="mt-10 text-sm text-neutral-600"><a href="{{ route('otp.form') }}" class="underline">Войдите</a>, чтобы добавить в корзину и оформить заказ.</p>
                @endauth

                <a href="{{ route('favorites.index') }}" class="mt-8 inline-block text-xs uppercase tracking-wider text-neutral-500 underline decoration-neutral-300 underline-offset-4 hover:text-black">Смотреть избранное</a>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const btn = document.querySelector('.favorite-btn');
        btn?.addEventListener('click', async () => {
            const id = Number(btn.dataset.id);
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch(`/favorites/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const data = await response.json();
            const added = data.status === 'added';
            const svg = btn.querySelector('.heart-icon');
            svg?.setAttribute('fill', added ? 'currentColor' : 'none');
            btn.classList.toggle('text-red-600', added);
            window.dyabNotify?.(data.message || (added ? 'Добавлено в избранное.' : 'Убрано из избранного.'), added ? 'success' : 'info');
        });

        const form = document.getElementById('add-cart-form');
        const submitBtn = document.getElementById('add-cart-btn');
        form?.querySelectorAll('input[name="size"]').forEach((r) => {
            r.addEventListener('change', () => {
                if (submitBtn?.dataset.soldOut === '1') return;
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'В корзину';
                }
            });
        });

    })();
</script>
@endpush
<div class="mt-8 grid gap-3 rounded-xl border border-neutral-200 bg-neutral-50 p-4 text-sm sm:mt-10 sm:grid-cols-2 sm:gap-4 sm:p-5">
    <div>
        <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Категория</p>
        <p class="mt-1 font-medium text-neutral-900">{{ $product->categoryModel?->name ?? (\App\Models\Product::CATEGORIES[$product->category] ?? $product->category) }}</p>
    </div>
    <div>
        <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Пол</p>
        <p class="mt-1 font-medium text-neutral-900">
            @if(($product->gender ?? 'unisex') === 'female') Женский
            @elseif(($product->gender ?? 'unisex') === 'male') Мужской
            @else Унисекс
            @endif
        </p>
    </div>
    <div>
        <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Цвет</p>
        <p class="mt-1 inline-flex items-center gap-2 font-medium text-neutral-900">
            @if($product->color)
            <span>{{ $product->color }}</span>
            @else
            <span>—</span>
            @endif
        </p>
    </div>
    <div>
        <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Наличие</p>
        <p class="mt-1 font-medium text-neutral-900">{{ $product->stock > 0 ? 'В наличии' : 'Нет в наличии' }}</p>
    </div>
</div>
@endsection