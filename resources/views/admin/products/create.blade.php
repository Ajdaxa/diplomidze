@extends('layouts.admin')

@section('title', 'Новый товар')
@section('heading', 'Добавление товара')

@section('content')
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Название</label>
            <input name="name" class="w-full rounded border border-neutral-300 px-3 py-2" required value="{{ old('name') }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Категория</label>
            <select name="category" class="w-full rounded border border-neutral-300 px-3 py-2" required>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('category', 'clothes') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Цена</label>
                <input name="price" type="number" class="w-full rounded border border-neutral-300 px-3 py-2" required value="{{ old('price') }}">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Остаток</label>
                <input name="stock" type="number" class="w-full rounded border border-neutral-300 px-3 py-2" required value="{{ old('stock', 0) }}">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Размеры (через запятую)</label>
            <input name="available_sizes" class="w-full rounded border border-neutral-300 px-3 py-2" placeholder="XS, S, M, L, XL" value="{{ old('available_sizes', 'XS, S, M, L, XL') }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Цвета для витрины (hex через запятую)</label>
            <input name="display_colors" class="w-full rounded border border-neutral-300 px-3 py-2" placeholder="#000000, #d4d4d4, #a3a3a3" value="{{ old('display_colors') }}">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Основной цвет (фильтр)</label>
                <input name="color" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('color') }}" placeholder="black">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Размер по умолчанию (опц.)</label>
                <input name="size" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('size') }}">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">URL фото</label>
            <input name="image" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('image') }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">или загрузите файл (основное)</label>
            <input type="file" name="image_file" accept="image/*" class="w-full rounded border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">URL фото при наведении</label>
            <input name="secondary_image" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('secondary_image') }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">или загрузите файл (hover)</label>
            <input type="file" name="secondary_image_file" accept="image/*" class="w-full rounded border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Описание</label>
            <textarea name="description" rows="4" class="w-full rounded border border-neutral-300 px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <div class="flex gap-4 text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_new_collection" value="1" @checked(old('is_new_collection'))> New Collection</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_limited_edition" value="1" @checked(old('is_limited_edition'))> Limited</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Активен</label>
        </div>
        <button type="submit" class="rounded bg-black px-6 py-3 text-sm font-medium uppercase tracking-wider text-white">Создать</button>
    </form>
@endsection
