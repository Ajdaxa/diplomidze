@extends('layouts.app')

@section('content')
    <div class="mb-6 rounded-xl border border-stone-200 bg-white p-4">
        <h1 class="mb-4 text-2xl font-semibold">Товары</h1>
        <form method="POST" action="{{ route('admin.products.store') }}" class="grid grid-cols-1 gap-3 md:grid-cols-2">
            @csrf
            <input name="name" placeholder="Название" class="rounded border border-stone-300 px-3 py-2" required>
            <input name="price" type="number" placeholder="Цена" class="rounded border border-stone-300 px-3 py-2" required>
            <input name="stock" type="number" placeholder="Остаток" class="rounded border border-stone-300 px-3 py-2" required>
            <input name="color" placeholder="Цвет (black/wine/...) " class="rounded border border-stone-300 px-3 py-2">
            <input name="size" placeholder="Размер (S/M/L/XL)" class="rounded border border-stone-300 px-3 py-2">
            <input name="image" placeholder="URL главного фото" class="rounded border border-stone-300 px-3 py-2">
            <input name="secondary_image" placeholder="URL hover фото" class="rounded border border-stone-300 px-3 py-2">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_new_collection" value="1">New Collection</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_limited_edition" value="1">Limited Edition</label>
            <textarea name="description" placeholder="Описание" class="md:col-span-2 rounded border border-stone-300 px-3 py-2"></textarea>
            <button class="md:col-span-2 rounded bg-stone-900 px-4 py-2 text-white">Создать товар</button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach($products as $product)
            <div class="rounded-xl border border-stone-200 bg-white p-4">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-2">
                    @csrf @method('PATCH')
                    <input name="name" value="{{ $product->name }}" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">
                    <div class="grid grid-cols-2 gap-2">
                        <input name="price" value="{{ $product->price }}" class="rounded border border-stone-300 px-2 py-1 text-sm">
                        <input name="stock" value="{{ $product->stock }}" class="rounded border border-stone-300 px-2 py-1 text-sm">
                    </div>
                    <input name="image" value="{{ $product->image }}" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">
                    <input name="secondary_image" value="{{ $product->secondary_image }}" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">
                    <textarea name="description" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">{{ $product->description }}</textarea>
                    <div class="flex items-center gap-3 text-xs">
                        <label><input type="checkbox" name="is_new_collection" value="1" @checked($product->is_new_collection)> new</label>
                        <label><input type="checkbox" name="is_limited_edition" value="1" @checked($product->is_limited_edition)> limited</label>
                        <label><input type="checkbox" name="is_active" value="1" @checked($product->is_active)> active</label>
                    </div>
                    <button class="rounded border border-stone-300 px-3 py-1 text-sm">Сохранить</button>
                </form>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                    @csrf @method('DELETE')
                    <button class="rounded border border-rose-300 px-3 py-1 text-sm text-rose-700">Удалить</button>
                </form>
            </div>
        @endforeach
    </div>
@endsection
