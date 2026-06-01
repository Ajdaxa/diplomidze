@extends('layouts.app')

@section('title', 'Каталог')

@section('content')
    @php $favoriteLookup = array_flip(array_map('intval', $favoriteIds ?? [])); @endphp

    <x-page-heading title="Каталог" lede="Фильтры, сортировка и вся коллекция в одном месте" />

    <div id="catalog" class="scroll-mt-24">
    <div class="tabs-scroll -mx-1 mb-0 flex snap-x snap-mandatory gap-2 overflow-x-auto border-b border-neutral-200 px-1 pb-4 lg:mx-0 lg:flex-wrap lg:overflow-visible lg:px-0">
        <button type="button" data-cat="" class="category-tab shrink-0 snap-start border border-black bg-black px-3 py-2.5 text-[11px] font-semibold uppercase tracking-wide text-white sm:px-4 sm:text-xs">Все</button>
        @foreach($categories as $cat)
            <button type="button" data-cat="{{ $cat->slug }}" class="category-tab shrink-0 snap-start border border-black bg-white px-3 py-2.5 text-[11px] font-semibold uppercase tracking-wide text-black hover:bg-neutral-50 sm:px-4 sm:text-xs">{{ $cat->name }}</button>
        @endforeach
    </div>

    <div class="flex flex-col gap-3 border-b border-neutral-200 bg-white py-3 sm:flex-row sm:items-center sm:justify-between sm:py-4">
        <button type="button" id="sort-toggle" class="flex min-h-11 w-full items-center justify-center gap-1 text-xs font-semibold uppercase tracking-wider text-black sm:w-auto sm:justify-start">
            Сортировка <span class="text-base leading-none">+</span>
        </button>
        <button type="button" id="filter-toggle" class="flex min-h-11 w-full items-center justify-center gap-2 text-xs font-semibold uppercase tracking-wider text-black sm:w-auto sm:justify-end">
            Фильтр
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h10M4 18h16"/></svg>
        </button>
    </div>

    <div id="sort-panel" class="hidden border-b border-neutral-200 bg-white px-2 py-3">
        <div class="flex flex-wrap gap-2">
            @foreach(['new' => 'Новинки', 'price-asc' => 'Цена ↑', 'price-desc' => 'Цена ↓', 'name' => 'Название'] as $sortKey => $sortLabel)
                <button type="button" class="sort-btn rounded border px-3 py-1.5 text-xs uppercase {{ ($filters['sort'] ?? 'new') === $sortKey ? 'border-black bg-black text-white' : 'border-neutral-300' }}" data-sort="{{ $sortKey }}">{{ $sortLabel }}</button>
            @endforeach
        </div>
    </div>

    <div id="filter-panel" class="hidden border-b border-neutral-200 bg-neutral-50 px-2 py-4">
        <input id="product-search" type="search" class="mb-3 w-full border border-neutral-300 bg-white px-3 py-2 text-sm" placeholder="Поиск (в т.ч. с опечатками)" value="{{ $filters['q'] ?? '' }}">
        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-neutral-500">Пол</p>
        <div class="mb-4 flex flex-wrap gap-2">
            @foreach(['' => 'Любой', 'female' => 'Женский', 'male' => 'Мужской', 'unisex' => 'Унисекс'] as $gKey => $gLabel)
                <button type="button" class="gender-filter border px-3 py-1 text-xs {{ ($filters['gender'] ?? '') === $gKey ? 'border-black bg-black text-white' : 'border-neutral-300' }}" data-gender="{{ $gKey }}">{{ $gLabel }}</button>
            @endforeach
        </div>
        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-neutral-500">Цена, ₽</p>
        <div class="mb-4 flex flex-wrap gap-2">
            <input type="number" id="filter-price-min" placeholder="от" class="w-24 border border-neutral-300 px-2 py-1.5 text-sm" value="{{ $filters['price_min'] ?? '' }}">
            <input type="number" id="filter-price-max" placeholder="до" class="w-24 border border-neutral-300 px-2 py-1.5 text-sm" value="{{ $filters['price_max'] ?? '' }}">
        </div>
        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-neutral-500">Размер одежды</p>
        <div class="mb-3 flex flex-wrap gap-2">
            <button type="button" class="size-filter border border-neutral-300 px-3 py-1 text-xs {{ ($filters['size'] ?? '') === '' ? 'bg-black text-white' : '' }}" data-size="">Все</button>
            @foreach(['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL'] as $sz)
                <button type="button" class="size-filter border border-neutral-300 px-3 py-1 text-xs {{ ($filters['size'] ?? '') === $sz ? 'bg-black text-white' : '' }}" data-size="{{ $sz }}">{{ $sz }}</button>
            @endforeach
        </div>
        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-neutral-500">Размер обуви</p>
        <div class="mb-4 flex flex-wrap gap-2">
            @foreach(['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'] as $sz)
                <button type="button" class="size-filter border border-neutral-300 px-3 py-1 text-xs {{ ($filters['size'] ?? '') === $sz ? 'bg-black text-white' : '' }}" data-size="{{ $sz }}">{{ $sz }}</button>
            @endforeach
        </div>
        <div class="flex flex-wrap gap-4 text-xs text-neutral-600">
            <label class="inline-flex items-center gap-2"><input type="checkbox" id="filter-in-stock" @checked($filters['in_stock'] ?? false)> Только в наличии</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" id="filter-new" @checked($filters['new'] ?? false)> Только новинки</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" id="filter-limited" @checked($filters['limited'] ?? false)> Только лимитированные</label>
        </div>
        <button type="button" id="filter-apply" class="mt-4 w-full bg-black py-2.5 text-xs font-semibold uppercase tracking-wider text-white sm:w-auto sm:px-8">Применить фильтры</button>
    </div>

    @if($products->isEmpty())
        <x-empty-state class="mt-8" title="Ничего не найдено" description="Сбросьте фильтры или измените параметры поиска.">
            <a href="{{ route('catalog') }}" class="inline-flex min-h-11 items-center justify-center border border-neutral-300 px-6 text-xs font-semibold uppercase tracking-[0.2em]">Сбросить</a>
        </x-empty-state>
    @endif

    <div id="skeleton-grid" class="mt-6 grid grid-cols-2 gap-x-[clamp(0.75rem,2vw,1.25rem)] gap-y-8 min-[520px]:grid-cols-[repeat(auto-fill,minmax(11rem,1fr))] xl:grid-cols-4 xl:gap-y-10">
        @for ($i = 0; $i < 8; $i++)
            <div class="animate-pulse">
                <div class="aspect-[3/4] bg-neutral-200"></div>
                <div class="mt-4 h-3 w-3/4 bg-neutral-200"></div>
                <div class="mt-2 h-3 w-1/3 bg-neutral-200"></div>
            </div>
        @endfor
    </div>

    <div id="products-grid" class="mt-6 {{ $products->isEmpty() ? 'hidden' : 'hidden' }} grid-cols-2 gap-x-[clamp(0.75rem,2vw,1.25rem)] gap-y-10 min-[520px]:grid-cols-[repeat(auto-fill,minmax(11rem,1fr))] xl:grid-cols-4 xl:gap-y-12">
        @foreach($products as $product)
            <article class="product-card group"
                     data-product='@json($product)'
                     data-id="{{ $product->id }}"
                     data-category="{{ $product->category_slug }}"
                     data-color="{{ $product->color }}"
                     data-gender="{{ $product->gender ?? 'unisex' }}"
                     data-stock="{{ $product->stock }}"
                     data-new="{{ $product->is_new_collection ? '1' : '0' }}"
                     data-limited="{{ $product->is_limited_edition ? '1' : '0' }}"
                     data-price="{{ $product->saleUnitPrice() }}"
                     data-sale="{{ $product->hasSale() ? '1' : '0' }}"
                     data-name="{{ $product->name }}"
                     data-sizes="{{ implode(',', $product->inStockSizes()) }}"
                     data-created="{{ $product->created_at?->timestamp ?? 0 }}">
                <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100">
                    <a href="{{ route('products.show', $product->slug) }}" class="absolute inset-0 z-0" aria-label="{{ $product->name }}"></a>
                    <img src="{{ $product->image ?: 'https://picsum.photos/800/1067?random='.$product->id }}" alt="" class="absolute inset-0 h-full w-full object-cover transition duration-500 ease-out group-hover:opacity-0">
                    <img src="{{ $product->secondary_image ?: ($product->image ?: 'https://picsum.photos/800/1067?random=' . ($product->id + 31)) }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-0 transition duration-500 ease-out group-hover:opacity-100">
                    @auth
                        <button type="button" class="favorite-btn absolute bottom-3 right-3 z-10 flex h-10 w-10 min-h-10 min-w-10 items-center justify-center rounded-full border border-neutral-200 bg-white/90 text-neutral-800 shadow-sm backdrop-blur-sm hover:bg-white {{ isset($favoriteLookup[$product->id]) ? 'text-red-600' : '' }}" data-id="{{ $product->id }}" data-active="{{ isset($favoriteLookup[$product->id]) ? '1' : '0' }}" aria-label="Избранное">
                            <svg class="heart-icon h-5 w-5" fill="{{ isset($favoriteLookup[$product->id]) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        </button>
                    @else
                        <a href="{{ route('otp.form', ['redirect' => request()->fullUrl()]) }}" class="absolute bottom-3 right-3 z-10 flex h-10 w-10 min-h-10 min-w-10 items-center justify-center rounded-full border border-neutral-200 bg-white/90 text-neutral-800 shadow-sm backdrop-blur-sm hover:bg-white" aria-label="Войдите для избранного">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        </a>
                    @endauth
                    <x-product-badge :product="$product" />
                </div>
                <div class="mt-4 space-y-1">
                    <h2 class="text-xs font-normal uppercase leading-snug tracking-wide text-black">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:underline">{{ $product->name }}</a>
                    </h2>
                    <x-product-price :product="$product" />
                    @if($product->stock < 1)
                        <p class="text-[10px] text-rose-700">Нет в наличии</p>
                    @elseif($product->stock < 3)
                        <p class="text-[10px] text-amber-700">Осталось: {{ $product->stock }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    @push('scripts')
    <script>
        const searchInput = document.getElementById('product-search');

        setTimeout(() => {
            document.getElementById('skeleton-grid')?.classList.add('hidden');
            const grid = document.getElementById('products-grid');
            if (grid && grid.querySelector('.product-card')) {
                grid.classList.remove('hidden');
                grid.classList.add('grid');
            }
        }, 450);

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

        let activeCategory = @json($filters['cat'] ?? '');
        let selectedGender = @json($filters['gender'] ?? '');
        let selectedSize = @json($filters['size'] ?? '');
        let sortMode = @json($filters['sort'] ?? 'new');
        let inStockOnly = @json($filters['in_stock'] ?? false);
        let onlyNew = @json($filters['new'] ?? false);
        let onlyLimited = @json($filters['limited'] ?? false);

        function buildCatalogUrl() {
            const p = new URLSearchParams();
            if (activeCategory) p.set('cat', activeCategory);
            if (sortMode && sortMode !== 'new') p.set('sort', sortMode);
            if (selectedGender) p.set('gender', selectedGender);
            if (selectedSize) p.set('size', selectedSize);
            const q = searchInput?.value?.trim();
            if (q) p.set('q', q);
            if (inStockOnly) p.set('in_stock', '1');
            if (onlyNew) p.set('new', '1');
            if (onlyLimited) p.set('limited', '1');
            const pmin = document.getElementById('filter-price-min')?.value;
            const pmax = document.getElementById('filter-price-max')?.value;
            if (pmin) p.set('price_min', pmin);
            if (pmax) p.set('price_max', pmax);
            const qs = p.toString();
            return '{{ route('catalog') }}' + (qs ? '?' + qs : '');
        }

        function reloadCatalog() {
            window.location.assign(buildCatalogUrl());
        }

        document.querySelectorAll('.category-tab').forEach((b) => {
            const on = (b.dataset.cat || '') === activeCategory;
            b.classList.toggle('bg-black', on);
            b.classList.toggle('text-white', on);
            b.classList.toggle('bg-white', !on);
            b.classList.toggle('text-black', !on);
        });

        document.querySelectorAll('.category-tab').forEach((btn) => {
            btn.addEventListener('click', () => {
                activeCategory = btn.dataset.cat || '';
                document.querySelectorAll('.category-tab').forEach((b) => {
                    const on = (b.dataset.cat || '') === activeCategory;
                    b.classList.toggle('bg-black', on);
                    b.classList.toggle('text-white', on);
                    b.classList.toggle('bg-white', !on);
                    b.classList.toggle('text-black', !on);
                });
                reloadCatalog();
            });
        });

        document.querySelectorAll('.size-filter').forEach((btn) => {
            btn.addEventListener('click', () => {
                selectedSize = btn.dataset.size || '';
                document.querySelectorAll('.size-filter').forEach((b) => {
                    b.classList.toggle('bg-black', b === btn);
                    b.classList.toggle('text-white', b === btn);
                });
            });
        });

        document.getElementById('filter-apply')?.addEventListener('click', reloadCatalog);

        document.getElementById('sort-toggle')?.addEventListener('click', () => {
            document.getElementById('sort-panel')?.classList.toggle('hidden');
        });
        document.getElementById('filter-toggle')?.addEventListener('click', () => {
            document.getElementById('filter-panel')?.classList.toggle('hidden');
        });

        document.querySelectorAll('.sort-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                sortMode = btn.dataset.sort || 'new';
                reloadCatalog();
            });
        });

        document.querySelectorAll('.gender-filter').forEach((btn) => {
            btn.addEventListener('click', () => {
                selectedGender = btn.dataset.gender || '';
                reloadCatalog();
            });
        });
        document.getElementById('filter-in-stock')?.addEventListener('change', (e) => {
            inStockOnly = !!e.target?.checked;
        });
        document.getElementById('filter-new')?.addEventListener('change', (e) => {
            onlyNew = !!e.target?.checked;
        });
        document.getElementById('filter-limited')?.addEventListener('change', (e) => {
            onlyLimited = !!e.target?.checked;
        });

        let searchTimer;
        searchInput?.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(reloadCatalog, 500);
        });
    </script>
    @endpush
    </div>
@endsection
