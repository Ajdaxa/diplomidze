@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-3xl font-semibold">Премиальная коллекция ДЯБ</h1>
        <input id="product-search" class="w-full rounded-lg border border-stone-300 px-3 py-2 md:w-80" placeholder="Поиск с учетом опечаток">
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-2">
        <div>
            <p class="mb-2 text-sm font-medium">Цвет</p>
            <div class="flex gap-2">
                <button class="color-filter h-7 w-7 rounded-full border border-stone-300 bg-black" data-color="black"></button>
                <button class="color-filter h-7 w-7 rounded-full border border-stone-300 bg-[#5b1f2a]" data-color="wine"></button>
                <button class="color-filter h-7 w-7 rounded-full border border-stone-300 bg-[#d6c08d]" data-color="gold"></button>
                <button class="color-filter rounded-full border border-stone-300 px-3 text-xs" data-color="">Сброс</button>
            </div>
        </div>
        <div>
            <p class="mb-2 text-sm font-medium">Размер</p>
            <div class="flex gap-2">
                <button class="size-filter rounded-md border border-stone-300 px-3 py-1 text-xs" data-size="S">S</button>
                <button class="size-filter rounded-md border border-stone-300 px-3 py-1 text-xs" data-size="M">M</button>
                <button class="size-filter rounded-md border border-stone-300 px-3 py-1 text-xs" data-size="L">L</button>
                <button class="size-filter rounded-md border border-stone-300 px-3 py-1 text-xs" data-size="">Все</button>
            </div>
        </div>
    </div>

    <div id="skeleton-grid" class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @for ($i = 0; $i < 6; $i++)
            <div class="animate-pulse rounded-xl border border-stone-200 bg-white p-4">
                <div class="mb-3 h-72 rounded-lg bg-stone-200"></div>
                <div class="mb-2 h-4 w-2/3 rounded bg-stone-200"></div>
                <div class="h-4 w-1/3 rounded bg-stone-200"></div>
            </div>
        @endfor
    </div>

    <div id="products-grid" class="hidden grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($products as $product)
            <article class="group rounded-xl border border-stone-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                     data-product='@json($product)'
                     data-color="{{ $product->color }}"
                     data-size="{{ $product->size }}">
                <div class="relative mb-3 overflow-hidden rounded-lg">
                    <img src="{{ $product->image ?: 'https://picsum.photos/800/1000?random='.$product->id }}" class="h-72 w-full object-cover transition duration-500 group-hover:opacity-0" alt="{{ $product->name }}">
                    <img src="{{ $product->secondary_image ?: ($product->image ?: 'https://picsum.photos/800/1000?random=' . ($product->id + 55)) }}" class="absolute inset-0 h-72 w-full object-cover opacity-0 transition duration-500 group-hover:opacity-100" alt="{{ $product->name }}">
                    <div class="absolute left-3 top-3 flex flex-col gap-2">
                        @if($product->is_new_collection)
                            <span class="bg-black px-2 py-1 text-[10px] uppercase tracking-wider text-white">New Collection</span>
                        @endif
                        @if($product->is_limited_edition)
                            <span class="bg-[#5b1f2a] px-2 py-1 text-[10px] uppercase tracking-wider text-white">Limited Edition</span>
                        @endif
                    </div>
                </div>
                <h2 class="mb-2 text-lg font-medium">
                    <a href="{{ route('products.show', $product->slug) }}" class="hover:underline">{{ $product->name }}</a>
                </h2>
                <p class="mb-2 text-sm text-stone-600">{{ $product->description }}</p>
                <p class="mb-3 text-xl font-semibold">{{ number_format($product->price, 2, '.', ' ') }} ₽</p>
                @if($product->stock < 3)
                    <p class="mb-2 text-xs text-amber-700">Осталось: {{ $product->stock }}</p>
                @endif
                <p class="mb-3 text-sm text-stone-500 delivery-timer"></p>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <button class="add-btn rounded-md bg-stone-900 px-3 py-2 text-sm text-white">В корзину</button>
                    </form>
                    <button class="compare-btn rounded-md border border-stone-300 px-3 py-2 text-sm" data-id="{{ $product->id }}">Сравнить</button>
                </div>
            </article>
        @endforeach
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('skeleton-grid')?.classList.add('hidden');
            const grid = document.getElementById('products-grid');
            grid?.classList.remove('hidden');
            grid?.classList.add('grid');
        }, 500);

        const cards = [...document.querySelectorAll('#products-grid article')];
        const productData = cards.map(c => JSON.parse(c.dataset.product));
        const fuse = new Fuse(productData, { keys: ['name', 'description'], threshold: 0.35 });
        const searchInput = document.getElementById('product-search');
        let selectedColor = '';
        let selectedSize = '';

        const applyFilters = () => {
            const q = searchInput?.value?.trim() || '';
            const ids = q ? fuse.search(q).map(r => r.item.id) : productData.map(p => p.id);
            cards.forEach(card => {
                const item = JSON.parse(card.dataset.product);
                const colorOk = !selectedColor || card.dataset.color === selectedColor;
                const sizeOk = !selectedSize || card.dataset.size === selectedSize;
                card.classList.toggle('hidden', !(ids.includes(item.id) && colorOk && sizeOk));
            });
        };

        searchInput?.addEventListener('input', applyFilters);
        document.querySelectorAll('.color-filter').forEach((btn) => btn.addEventListener('click', () => {
            selectedColor = btn.dataset.color;
            applyFilters();
        }));
        document.querySelectorAll('.size-filter').forEach((btn) => btn.addEventListener('click', () => {
            selectedSize = btn.dataset.size;
            applyFilters();
        }));

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
                el.textContent = `Закажи в течение ${hours}:${mins}, чтобы получить ${targetDay}`;
            });
        };
        tickDeliveryTimer();
        setInterval(tickDeliveryTimer, 30000);

        document.querySelectorAll('.add-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const original = btn.textContent;
                btn.textContent = 'Добавлено!';
                btn.classList.add('bg-emerald-700');
                setTimeout(() => {
                    btn.textContent = original;
                    btn.classList.remove('bg-emerald-700');
                }, 1000);
            });
        });

        const key = 'dyab-compare';
        const readCompare = () => JSON.parse(localStorage.getItem(key) || '[]');
        document.querySelectorAll('.compare-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const compare = new Set(readCompare());
                compare.add(Number(btn.dataset.id));
                localStorage.setItem(key, JSON.stringify([...compare]));
                window.dyabToast?.('Товар добавлен в сравнение');
            });
        });
    </script>
@endsection
