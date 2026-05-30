@extends('layouts.admin')

@section('title', 'Скидки на товары')
@section('heading', 'Скидки на товары')

@section('content')
    <p class="mb-6 max-w-2xl text-sm text-neutral-600">
        Здесь задаются скидки на выбранные позиции каталога. В списке «Товары» отображается только текущий размер скидки — изменить его можно только на этой странице.
    </p>

    @if($onSale->isNotEmpty())
        <div class="mb-8 rounded-xl border border-rose-100 bg-rose-50/50 p-4 sm:p-5">
            <h2 class="text-xs font-semibold uppercase tracking-wider text-rose-800">Сейчас со скидкой ({{ $onSale->count() }})</h2>
            <ul class="mt-3 flex flex-wrap gap-2">
                @foreach($onSale as $product)
                    <li class="rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-xs">
                        <span class="font-medium text-neutral-900">{{ $product->name }}</span>
                        <span class="ml-1 font-semibold text-rose-700">−{{ $product->sale_percent }}%</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.sales.apply') }}" id="sales-form" class="rounded-xl border border-neutral-200 bg-white p-4 sm:p-6">
        @csrf
        <div class="flex flex-col gap-4 border-b border-neutral-100 pb-5 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Новая скидка</p>
                <label class="mt-2 block text-xs text-neutral-600">Размер скидки, %</label>
                <input type="number" name="sale_percent" id="sale-percent" min="1" max="90"
                       class="mt-1 w-32 rounded-lg border border-neutral-300 px-3 py-2 text-sm @error('sale_percent') border-rose-400 @enderror"
                       placeholder="20" value="{{ old('sale_percent') }}">
                @error('sale_percent')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" name="action" value="apply" class="rounded-lg bg-black px-5 py-2.5 text-sm font-medium text-white hover:bg-neutral-800">
                    Применить к выбранным
                </button>
                <button type="submit" name="action" value="clear" class="rounded-lg border border-neutral-300 px-5 py-2.5 text-sm hover:border-neutral-900">
                    Снять скидку
                </button>
            </div>
        </div>

        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Выберите товары</p>
            <input type="search" id="sales-product-search" placeholder="Поиск по названию или артикулу…"
                   class="w-full max-w-md rounded-lg border border-neutral-300 px-3 py-2 text-sm sm:w-auto">
        </div>

        <div class="mt-3 flex items-center gap-2 border-b border-neutral-100 pb-3">
            <input type="checkbox" id="select-all-sales" class="rounded border-neutral-300">
            <label for="select-all-sales" class="text-xs text-neutral-600">Выбрать все</label>
        </div>

        <div class="max-h-[min(28rem,60vh)] overflow-y-auto">
            <table class="w-full text-left text-sm">
                <thead class="sticky top-0 bg-white text-xs uppercase tracking-wider text-neutral-500 shadow-sm">
                    <tr>
                        <th class="w-10 py-2"></th>
                        <th class="py-2 pr-3">Товар</th>
                        <th class="py-2 pr-3">Цена</th>
                        <th class="py-2">Текущая скидка</th>
                    </tr>
                </thead>
                <tbody id="sales-products-body">
                    @foreach($products as $product)
                        <tr class="sales-product-row border-b border-neutral-50" data-search="{{ mb_strtolower($product->name.' '.($product->sku ?? '')) }}">
                            <td class="py-2.5">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="sales-product-cb rounded border-neutral-300">
                            </td>
                            <td class="py-2.5 pr-3">
                                <p class="font-medium">{{ $product->name }}</p>
                                @if($product->sku)
                                    <p class="font-mono text-[10px] text-neutral-500">{{ $product->sku }}</p>
                                @endif
                            </td>
                            <td class="py-2.5 pr-3 text-neutral-700">{{ number_format($product->price, 0, '.', ' ') }} ₽</td>
                            <td class="py-2.5">
                                @if($product->hasSale())
                                    <span class="rounded bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700">−{{ $product->sale_percent }}%</span>
                                @else
                                    <span class="text-neutral-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>

    @push('scripts')
    <script>
        document.getElementById('select-all-sales')?.addEventListener('change', (e) => {
            document.querySelectorAll('.sales-product-row:not([hidden]) .sales-product-cb').forEach((cb) => {
                cb.checked = e.target.checked;
            });
        });

        document.getElementById('sales-product-search')?.addEventListener('input', (e) => {
            const q = e.target.value.trim().toLowerCase();
            document.querySelectorAll('.sales-product-row').forEach((row) => {
                const hay = row.dataset.search || '';
                row.hidden = q !== '' && !hay.includes(q);
            });
        });

    </script>
    @endpush
@endsection
