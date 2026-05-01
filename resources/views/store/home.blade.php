@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-semibold">Премиальная коллекция ДЯБ</h1>
        <input id="product-search" class="w-80 rounded-lg border border-stone-300 px-3 py-2" placeholder="Поиск с учетом опечаток">
    </div>

    <div id="products-grid" class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($products as $product)
            <article class="rounded-xl border border-stone-200 bg-white p-4 shadow-sm" data-product='@json($product)'>
                <h2 class="mb-2 text-lg font-medium">{{ $product->name }}</h2>
                <p class="mb-2 text-sm text-stone-600">{{ $product->description }}</p>
                <p class="mb-3 text-xl font-semibold">{{ number_format($product->price, 2, '.', ' ') }} ₽</p>
                <p class="mb-3 text-sm text-stone-500 delivery-date"></p>
                <button class="compare-btn rounded-md bg-stone-900 px-3 py-2 text-sm text-white" data-id="{{ $product->id }}">Сравнить</button>
            </article>
        @endforeach
    </div>

    <script>
        const cards = [...document.querySelectorAll('#products-grid article')];
        const productData = cards.map(c => JSON.parse(c.dataset.product));
        const fuse = new Fuse(productData, { keys: ['name', 'description'], threshold: 0.35 });
        const searchInput = document.getElementById('product-search');

        searchInput?.addEventListener('input', (e) => {
            const q = e.target.value.trim();
            const ids = q ? fuse.search(q).map(r => r.item.id) : productData.map(p => p.id);
            cards.forEach(card => {
                const item = JSON.parse(card.dataset.product);
                card.classList.toggle('hidden', !ids.includes(item.id));
            });
        });

        const isTomorrow = new Date().getHours() < 16;
        document.querySelectorAll('.delivery-date').forEach((el) => {
            el.textContent = isTomorrow ? 'Доставка: завтра' : 'Доставка: послезавтра';
        });

        const key = 'dyab-compare';
        const readCompare = () => JSON.parse(localStorage.getItem(key) || '[]');
        document.querySelectorAll('.compare-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const compare = new Set(readCompare());
                compare.add(Number(btn.dataset.id));
                localStorage.setItem(key, JSON.stringify([...compare]));
                btn.textContent = 'Добавлено к сравнению';
            });
        });
    </script>
@endsection
