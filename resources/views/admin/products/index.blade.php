@extends('layouts.admin')

@section('title', 'Товары')
@section('heading', 'Каталог товаров')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-neutral-600">Список товаров. Редактирование и удаление — отдельными действиями.</p>
        <a href="{{ route('admin.products.create') }}" class="rounded bg-black px-4 py-2 text-sm font-medium uppercase tracking-wider text-white">Добавить товар</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase tracking-wider text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Название</th>
                    <th class="px-4 py-3">Категория</th>
                    <th class="px-4 py-3">Цена</th>
                    <th class="px-4 py-3">Остаток</th>
                    <th class="px-4 py-3 text-right">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="border-b border-neutral-100 last:border-0">
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-neutral-600">{{ \App\Models\Product::CATEGORIES[$product->category] ?? $product->category }}</td>
                        <td class="px-4 py-3">{{ number_format($product->price, 0, '.', ' ') }} ₽</td>
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
