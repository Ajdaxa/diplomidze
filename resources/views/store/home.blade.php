@extends('layouts.app')

@section('content')
    {{-- Категории — как на референсе --}}
    <div class="mb-0 flex flex-wrap gap-2 border-b border-neutral-200 pb-4">
        <button type="button" data-cat="" class="category-tab border border-black bg-black px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white">Все</button>
        <button type="button" data-cat="clothes" class="category-tab border border-black bg-white px-4 py-2 text-xs font-semibold uppercase tracking-wide text-black hover:bg-neutral-50">Одежда</button>
        <button type="button" data-cat="accessories" class="category-tab border border-black bg-white px-4 py-2 text-xs font-semibold uppercase tracking-wide text-black hover:bg-neutral-50">Аксессуары</button>
        <button type="button" data-cat="shoes" class="category-tab border border-black bg-white px-4 py-2 text-xs font-semibold uppercase tracking-wide text-black hover:bg-neutral-50">Обувь</button>
        <button type="button" data-cat="sportswear" class="category-tab border border-black bg-white px-4 py-2 text-xs font-semibold uppercase tracking-wide text-black hover:bg-neutral-50">Спорт</button>
        <button type="button" data-cat="home" class="category-tab border border-black bg-white px-4 py-2 text-xs font-semibold uppercase tracking-wide text-black hover:bg-neutral-50">Дом</button>
    </div>

    <div class="flex items-center gap-3 border-b border-neutral-200 bg-neutral-100 px-2 py-3 text-sm text-neutral-800">
        <input type="checkbox" id="pickup-mock" class="h-4 w-4 rounded border-neutral-400">
        <label for="pickup-mock" class="cursor-pointer select-none">Самовывоз сегодня: <span class="underline underline-offset-2">выбрать точку</span></label>
    </div>

    <div class="flex items-center justify-between border-b border-neutral-200 bg-white py-4">
        <button type="button" id="sort-toggle" class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-black">
            Сортировка <span class="text-base leading-none">+</span>
        </button>
        <button type="button" id="filter-toggle" class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-black">
            Фильтр
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h10M4 18h16"/></svg>
        </button>
    </div>

    <div id="sort-panel" class="hidden border-b border-neutral-200 bg-white px-2 py-3">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="sort-btn rounded border border-neutral-300 px-3 py-1.5 text-xs uppercase" data-sort="new">Новинки</button>
            <button type="button" class="sort-btn rounded border border-neutral-300 px-3 py-1.5 text-xs uppercase" data-sort="price-asc">Цена ↑</button>
            <button type="button" class="sort-btn rounded border border-neutral-300 px-3 py-1.5 text-xs uppercase" data-sort="price-desc">Цена ↓</button>
            <button type="button" class="sort-btn rounded border border-neutral-300 px-3 py-1.5 text-xs uppercase" data-sort="name">Название</button>
        </div>
    </div>

    <div id="filter-panel" class="hidden border-b border-neutral-200 bg-neutral-50 px-2 py-4">
        <input id="product-search" type="search" class="mb-3 w-full border border-neutral-300 bg-white px-3 py-2 text-sm" placeholder="Поиск (в т.ч. с опечатками)">
        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-neutral-500">Цвет</p>
        <div class="mb-4 flex flex-wrap gap-2">
            <button type="button" class="color-filter h-8 w-8 border border-neutral-900 bg-black" data-color="black" title="black"></button>
            <button type="button" class="color-filter h-8 w-8 border border-neutral-300 bg-[#5b1f2a]" data-color="wine"></button>
            <button type="button" class="color-filter h-8 w-8 border border-neutral-300 bg-[#d6c08d]" data-color="gold"></button>
            <button type="button" class="color-filter border border-neutral-300 px-3 py-1 text-xs" data-color="">Все</button>
        </div>
    </div>

    <div id="skeleton-grid" class="mt-8 grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 lg:grid-cols-4">
        @for ($i = 0; $i < 8; $i++)
            <div class="animate-pulse">
                <div class="aspect-[3/4] bg-neutral-200"></div>
                <div class="mt-4 h-3 w-3/4 bg-neutral-200"></div>
                <div class="mt-2 h-3 w-1/3 bg-neutral-200"></div>
            </div>
        @endfor
    </div>

    <div id="products-grid" class="mt-8 hidden grid-cols-2 gap-x-4 gap-y-12 md:grid-cols-3 lg:grid-cols-4">
        @foreach($products as $product)
            @php
                $colors = $product->colorsForCard();
                $swatchShow = array_slice($colors, 0, 3);
                $more = max(0, count($colors) - 3);
            @endphp
            <article class="product-card group"
                     data-product='@json($product)'
                     data-id="{{ $product->id }}"
                     data-category="{{ $product->category }}"
                     data-color="{{ $product->color }}"
                     data-price="{{ $product->price }}"
                     data-name="{{ $product->name }}"
                     data-created="{{ $product->created_at?->timestamp ?? 0 }}">
                <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100">
                    <a href="{{ route('products.show', $product->slug) }}" class="absolute inset-0 z-0" aria-label="{{ $product->name }}"></a>
                    <img src="{{ $product->image ?: 'https://picsum.photos/800/1067?random='.$product->id }}" alt="" class="absolute inset-0 h-full w-full object-cover transition duration-500 ease-out group-hover:opacity-0">
                    <img src="{{ $product->secondary_image ?: ($product->image ?: 'https://picsum.photos/800/1067?random=' . ($product->id + 31)) }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-0 transition duration-500 ease-out group-hover:opacity-100">
                    <button type="button" class="favorite-btn absolute bottom-3 right-3 z-10 flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 bg-white/90 text-neutral-800 shadow-sm backdrop-blur-sm hover:bg-white" data-id="{{ $product->id }}" aria-label="Избранное">
                        <svg class="heart-icon h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    </button>
                    @if($product->is_new_collection)
                        <span class="absolute left-2 top-2 bg-black px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-white">New</span>
                    @endif
                </div>
                <div class="mt-4 space-y-1">
                    <h2 class="text-xs font-normal uppercase leading-snug tracking-wide text-black">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:underline">{{ $product->name }}</a>
                    </h2>
                    <p class="text-sm font-bold text-black">{{ number_format($product->price, 0, '.', ' ') }} ₽</p>
                    <div class="flex items-center gap-1 pt-1">
                        @foreach($swatchShow as $c)
                            @php $isHex = is_string($c) && str_starts_with($c, '#'); @endphp
                            <span class="h-2.5 w-2.5 border border-neutral-300 {{ $isHex ? '' : 'bg-neutral-300' }}" @if($isHex) style="background-color: {{ $c }}" @endif></span>
                        @endforeach
                        @if($more > 0)<span class="pl-1 text-[10px] text-neutral-500">+{{ $more }}</span>@endif
                    </div>
                    @if($product->stock < 3)
                        <p class="text-[10px] text-amber-700">Осталось: {{ $product->stock }}</p>
                    @endif
                    <p class="delivery-timer text-[10px] text-neutral-500"></p>
                    <button type="button" class="compare-btn mt-2 text-[10px] font-semibold uppercase tracking-wider text-neutral-500 underline decoration-neutral-300 underline-offset-4 hover:text-black" data-id="{{ $product->id }}">Сравнить</button>
                </div>
            </article>
        @endforeach
    </div>

    @push('scripts')
    <script>
        setTimeout(() => {
            document.getElementById('skeleton-grid')?.classList.add('hidden');
            const grid = document.getElementById('products-grid');
            grid?.classList.remove('hidden');
            grid?.classList.add('grid');
        }, 450);

        const FAV_KEY = 'dyab-favorites';
        const readFav = () => new Set(JSON.parse(localStorage.getItem(FAV_KEY) || '[]'));
        const writeFav = (s) => localStorage.setItem(FAV_KEY, JSON.stringify([...s]));

        function syncHearts() {
            const fav = readFav();
            document.querySelectorAll('.favorite-btn').forEach((btn) => {
                const id = Number(btn.dataset.id);
                const svg = btn.querySelector('.heart-icon');
                if (fav.has(id)) {
                    svg?.setAttribute('fill', 'currentColor');
                    btn.classList.add('text-red-600');
                } else {
                    svg?.setAttribute('fill', 'none');
                    btn.classList.remove('text-red-600');
                }
            });
        }
        syncHearts();
        document.querySelectorAll('.favorite-btn').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = Number(btn.dataset.id);
                const fav = readFav();
                if (fav.has(id)) fav.delete(id); else fav.add(id);
                writeFav(fav);
                syncHearts();
                window.dyabToast?.(fav.has(id) ? 'В избранном' : 'Убрано из избранного');
            });
        });

        let activeCategory = '';
        let selectedColor = '';
        let sortMode = 'new';
        const cards = () => [...document.querySelectorAll('#products-grid .product-card')];
        const productData = () => cards().map(c => JSON.parse(c.dataset.product));
        let fuse;
        function buildFuse() {
            fuse = new Fuse(productData(), { keys: ['name', 'description'], threshold: 0.38 });
        }
        buildFuse();

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
                applyFilters();
            });
        });

        document.getElementById('sort-toggle')?.addEventListener('click', () => {
            document.getElementById('sort-panel')?.classList.toggle('hidden');
        });
        document.getElementById('filter-toggle')?.addEventListener('click', () => {
            document.getElementById('filter-panel')?.classList.toggle('hidden');
        });

        document.querySelectorAll('.sort-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                sortMode = btn.dataset.sort || 'new';
                applyFilters();
            });
        });

        document.querySelectorAll('.color-filter').forEach((btn) => {
            btn.addEventListener('click', () => {
                selectedColor = btn.dataset.color || '';
                applyFilters();
            });
        });

        const searchInput = document.getElementById('product-search');
        searchInput?.addEventListener('input', applyFilters);

        function applyFilters() {
            const q = searchInput?.value?.trim() || '';
            const ids = q ? fuse.search(q).map(r => r.item.id) : productData().map(p => p.id);
            const idSet = new Set(ids);

            cards().forEach((card) => {
                const item = JSON.parse(card.dataset.product);
                const catOk = !activeCategory || card.dataset.category === activeCategory;
                const colorOk = !selectedColor || card.dataset.color === selectedColor;
                card.classList.toggle('hidden', !(idSet.has(item.id) && catOk && colorOk));
            });

            sortCards();
        }

        function sortCards() {
            const grid = document.getElementById('products-grid');
            if (!grid) return;
            const list = [...grid.querySelectorAll('.product-card')];
            const cmp = (a, b) => {
                if (sortMode === 'price-asc') return Number(a.dataset.price) - Number(b.dataset.price);
                if (sortMode === 'price-desc') return Number(b.dataset.price) - Number(a.dataset.price);
                if (sortMode === 'name') return a.dataset.name.localeCompare(b.dataset.name);
                return Number(b.dataset.created) - Number(a.dataset.created);
            };
            list.sort(cmp);
            list.forEach((el) => grid.appendChild(el));
        }

        const tickDeliveryTimer = () => {
            const now = new Date();
            const cutoff = new Date();
            cutoff.setHours(16, 0, 0, 0);
            const diff = cutoff - now;
            const targetDay = diff > 0 ? 'завтра' : 'послезавтра';
            const remain = diff > 0 ? diff : (24 * 60 * 60 * 1000 + diff);
            const hours = String(Math.floor(remain / 3600000)).padStart(2, '0');
            const mins = String(Math.floor((remain % 3600000) / 60000)).padStart(2, '0');
            document.querySelectorAll('.delivery-timer').forEach((el) => {
                el.textContent = `Закажи за ${hours}:${mins} — доставка ${targetDay}`;
            });
        };
        tickDeliveryTimer();
        setInterval(tickDeliveryTimer, 30000);

        const key = 'dyab-compare';
        const readCompare = () => JSON.parse(localStorage.getItem(key) || '[]');
        document.querySelectorAll('.compare-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const compare = new Set(readCompare());
                compare.add(Number(btn.dataset.id));
                localStorage.setItem(key, JSON.stringify([...compare]));
                window.dyabToast?.('Добавлено в сравнение');
            });
        });

        applyFilters();
    </script>
    @endpush
@endsection
