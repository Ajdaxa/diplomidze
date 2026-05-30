@extends('layouts.admin')

@section('title', 'Товары')
@section('heading', 'Каталог товаров')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-neutral-600">
            Список товаров. Скидки задаются в разделе
            <a href="{{ route('admin.sales.index') }}" class="font-medium text-black underline">«Скидки на товары»</a>.
        </p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.sales.index') }}" class="rounded border border-neutral-300 px-4 py-2 text-sm font-medium hover:border-black">Скидки</a>
            <a href="{{ route('admin.products.create') }}" class="rounded bg-black px-4 py-2 text-sm font-medium uppercase tracking-wider text-white">Добавить товар</a>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase tracking-wider text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Товар</th>
                    <th class="px-4 py-3">Артикул</th>
                    <th class="px-4 py-3">Категория</th>
                    <th class="px-4 py-3">Цена</th>
                    <th class="px-4 py-3">Скидка</th>
                    <th class="px-4 py-3">Остаток</th>
                    <th class="px-4 py-3 text-right">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="border-b border-neutral-100 last:border-0">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-10 shrink-0 overflow-hidden rounded bg-neutral-100">
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="" class="h-full w-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium">{{ $product->name }}</p>
                                    @if(!$product->is_active)
                                        <span class="text-[10px] uppercase tracking-wider text-rose-600">Скрыт</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-neutral-600">{{ $product->sku ?? '—' }}</td>
                        <td class="px-4 py-3 text-neutral-600">{{ $product->categoryModel?->name ?? (\App\Models\Product::CATEGORIES[$product->category] ?? $product->category) }}</td>
                        <td class="px-4 py-3">
                            @if($product->hasSale())
                                <span class="text-neutral-400 line-through">{{ number_format($product->price, 0, '.', ' ') }}</span>
                                <span class="ml-1 font-medium text-rose-700">{{ number_format($product->saleUnitPrice(), 0, '.', ' ') }} ₽</span>
                            @else
                                {{ number_format($product->price, 0, '.', ' ') }} ₽
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($product->hasSale())
                                <span class="rounded bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700">−{{ $product->sale_percent }}%</span>
                            @else
                                <span class="text-neutral-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $product->stock }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.products.edit', $product) }}" class="mr-2 text-neutral-600 hover:text-black">Изменить</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Удалить товар?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:underline">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
@endsection
