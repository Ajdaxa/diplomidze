@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', \Illuminate\Support\Str::limit($product->description ?: $product->name, 155))

@php
$sizes = $product->sizesList();
$categoryLabel = $product->categoryModel?->name ?? (\App\Models\Product::CATEGORIES[$product->category] ?? $product->category);
$categorySlug = $product->category_slug;
$catalogCategoryUrl = route('catalog').'?cat='.rawurlencode($categorySlug);
@endphp

@section('content')
<nav class="mb-6 sm:mb-8" aria-label="Навигация по разделам">
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] font-medium uppercase tracking-[0.16em] text-neutral-500">
        <li class="flex min-h-9 items-center">
            <a href="{{ route('home') }}" class="rounded-md px-1.5 py-1 text-neutral-600 transition hover:bg-neutral-100 hover:text-black">Главная</a>
        </li>
        <li class="flex items-center text-neutral-300" aria-hidden="true">
            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        </li>
        <li class="flex min-h-9 items-center">
            <a href="{{ route('catalog') }}" class="rounded-md px-1.5 py-1 text-neutral-600 transition hover:bg-neutral-100 hover:text-black">Каталог</a>
        </li>
        <li class="flex items-center text-neutral-300" aria-hidden="true">
            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        </li>
        <li class="flex min-h-9 items-center">
            <a href="{{ $catalogCategoryUrl }}" class="max-w-[12rem] truncate rounded-md px-1.5 py-1 text-neutral-600 transition hover:bg-neutral-100 hover:text-black sm:max-w-xs" title="{{ $categoryLabel }}">{{ $categoryLabel }}</a>
        </li>
        <li class="flex items-center text-neutral-300" aria-hidden="true">
            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        </li>
        <li class="flex min-h-9 max-w-full items-center">
            <span class="truncate rounded-md bg-neutral-100 px-2 py-1 text-neutral-900" aria-current="page">{{ $product->name }}</span>
        </li>
    </ol>
</nav>

<div class="grid grid-cols-1 gap-8 sm:gap-10 lg:grid-cols-2 lg:items-start lg:gap-12 xl:gap-16">
    <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100 sm:aspect-[3/4] lg:sticky lg:top-20 lg:aspect-auto lg:min-h-[min(85vh,40rem)] xl:min-h-[640px]">
        <img src="{{ $product->image ?: 'https://picsum.photos/1000/1333?random='.$product->id }}" alt="{{ $product->name }}" class="absolute inset-0 h-full w-full object-cover">
        <x-product-badge :product="$product" class="left-4 top-4" />
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
        @if($product->sku)
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Артикул</span>
                <button type="button" id="copy-sku-btn" data-sku="{{ $product->sku }}" class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 font-mono text-xs text-neutral-800 transition hover:border-neutral-400 hover:bg-white" title="Скопировать артикул">
                    <span id="sku-text">{{ $product->sku }}</span>
                    <svg class="h-3.5 w-3.5 shrink-0 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
        @endif
        <div class="mt-4">
            @if($product->hasSale())
                <p class="mb-2 inline-flex items-center gap-2 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-rose-700">
                    <span>Скидка −{{ (int) $product->sale_percent }}%</span>
                    <span class="text-neutral-500">экономия {{ number_format($product->price - $product->saleUnitPrice(), 0, '.', ' ') }} ₽</span>
                </p>
            @endif
            <x-product-price :product="$product" size="lg" />
        </div>
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

        <div class="mt-8 grid gap-3 rounded-xl border border-neutral-200 bg-neutral-50/80 p-4 text-sm backdrop-blur-sm sm:mt-10 sm:grid-cols-2 sm:gap-4 sm:p-5">
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
            <div class="relative w-full max-w-[7.5rem]">
                <input type="number" name="quantity" id="product-qty" value="1" min="1" max="20" step="1" placeholder=" " class="peer block w-full rounded-xl border border-neutral-200 bg-white px-3.5 pb-2.5 pt-5 text-sm text-neutral-900 shadow-sm outline-none transition-[border-color,box-shadow] duration-200 ease-out placeholder:text-transparent focus:border-black focus:ring-2 focus:ring-black/[0.06]">
                <label for="product-qty" class="pointer-events-none absolute left-3.5 top-1/2 origin-left -translate-y-1/2 text-[15px] text-neutral-500 transition-all duration-200 ease-out peer-focus:top-2 peer-focus:translate-y-0 peer-focus:text-[11px] peer-focus:font-medium peer-focus:tracking-wide peer-focus:text-neutral-700 peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:translate-y-0 peer-[:not(:placeholder-shown)]:text-[11px] peer-[:not(:placeholder-shown)]:font-medium peer-[:not(:placeholder-shown)]:tracking-wide peer-[:not(:placeholder-shown)]:text-neutral-700">Количество</label>
            </div>
            <button type="submit" id="add-cart-btn" data-sold-out="{{ $product->stock < 1 ? '1' : '0' }}" @disabled($product->stock < 1) class="w-full min-h-12 bg-black py-3.5 text-xs font-semibold uppercase tracking-[0.2em] text-white transition enabled:hover:bg-neutral-800 disabled:cursor-not-allowed disabled:bg-neutral-300 sm:min-h-[3.25rem] sm:py-4">
                {{ $product->stock < 1 ? 'Нет в наличии' : 'Выберите размер' }}
            </button>
        </form>
        @else
        <p class="mt-10 text-sm text-neutral-600"><a href="{{ route('otp.form') }}" class="underline">Войдите</a>, чтобы добавить в корзину и оформить заказ.</p>
        @endauth

        <details class="mt-8 group rounded-xl border border-neutral-200 bg-white">
            <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-xs font-semibold uppercase tracking-wider text-neutral-800 [&::-webkit-details-marker]:hidden">
                Доставка и оплата
                <span class="text-neutral-400 transition group-open:rotate-45">+</span>
            </summary>
            <div class="border-t border-neutral-100 px-4 py-3 text-sm leading-relaxed text-neutral-600">
                <p>Курьерская доставка по городу. Оплата картой через YooMoney после оформления заказа.</p>
                <p class="mt-2 text-xs text-neutral-500">Срок и стоимость уточняются при подтверждении заказа.</p>
            </div>
        </details>

        <a href="{{ route('favorites.index') }}" class="mt-6 inline-block text-xs uppercase tracking-wider text-neutral-500 underline decoration-neutral-300 underline-offset-4 hover:text-black">Смотреть избранное</a>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        document.getElementById('copy-sku-btn')?.addEventListener('click', async () => {
            const sku = document.getElementById('copy-sku-btn')?.dataset.sku;
            if (!sku) return;
            try {
                await navigator.clipboard.writeText(sku);
                window.dyabNotify?.('Артикул скопирован', 'success');
            } catch {
                window.dyabNotify?.('Не удалось скопировать', 'warn');
            }
        });

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
@endsection
