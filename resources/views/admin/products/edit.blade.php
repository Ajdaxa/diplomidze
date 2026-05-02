@extends('layouts.admin')

@section('title', 'Редактирование')
@section('heading', 'Редактирование товара')

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}" class="mx-auto max-w-2xl space-y-4">
        @csrf @method('PATCH')
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Название</label>
            <input name="name" class="w-full rounded border border-neutral-300 px-3 py-2" required value="{{ old('name', $product->name) }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Категория</label>
            <select name="category" class="w-full rounded border border-neutral-300 px-3 py-2" required>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('category', $product->category) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Цена</label>
                <input name="price" type="number" class="w-full rounded border border-neutral-300 px-3 py-2" required value="{{ old('price', $product->price) }}">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Остаток</label>
                <input name="stock" type="number" class="w-full rounded border border-neutral-300 px-3 py-2" required value="{{ old('stock', $product->stock) }}">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Размеры (через запятую)</label>
            <input name="available_sizes" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('available_sizes', is_array($product->available_sizes) ? implode(', ', $product->available_sizes) : '') }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Цвета для витрины</label>
            <input name="display_colors" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('display_colors', is_array($product->display_colors) ? implode(', ', $product->display_colors) : '') }}">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Цвет (фильтр)</label>
                <input name="color" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('color', $product->color) }}">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Размер по умолчанию</label>
                <input name="size" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('size', $product->size) }}">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">URL фото</label>
            <input name="image" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('image', $product->image) }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">URL hover</label>
            <input name="secondary_image" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('secondary_image', $product->secondary_image) }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Описание</label>
            <textarea name="description" rows="4" class="w-full rounded border border-neutral-300 px-3 py-2">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="flex gap-4 text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_new_collection" value="1" @checked(old('is_new_collection', $product->is_new_collection))> New</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_limited_edition" value="1" @checked(old('is_limited_edition', $product->is_limited_edition))> Limited</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Активен</label>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="rounded bg-black px-6 py-3 text-sm font-medium uppercase tracking-wider text-white">Сохранить</button>
            <a href="{{ route('admin.products.index') }}" class="rounded border border-neutral-300 px-6 py-3 text-sm">Назад</a>
        </div>
    </form>
@endsection
