@extends('layouts.admin')

@section('title', 'Редактирование')
@section('heading', 'Редактирование товара')

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-4">
        @csrf @method('PATCH')
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Артикул</label>
            <input type="text" readonly class="w-full cursor-default rounded border border-neutral-200 bg-neutral-50 px-3 py-2 font-mono text-sm text-neutral-700" value="{{ $product->sku ?? 'Будет сгенерирован при сохранении' }}">
            <p class="mt-1 text-[11px] text-neutral-500">Генерируется автоматически, редактирование не требуется.</p>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Название</label>
            <input name="name" class="w-full rounded border border-neutral-300 px-3 py-2" required value="{{ old('name', $product->name) }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Категория</label>
            <select name="category_id" class="w-full rounded border border-neutral-300 px-3 py-2" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((int) old('category_id', $product->category_id) === $cat->id)>{{ $cat->name }}</option>
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
        @if($product->hasSale())
            <p class="rounded-lg border border-rose-100 bg-rose-50 px-3 py-2 text-xs text-rose-800">
                Активная скидка: <strong>−{{ $product->sale_percent }}%</strong>.
                Изменить — в <a href="{{ route('admin.sales.index') }}" class="font-semibold underline">«Скидки на товары»</a>.
            </p>
        @else
            <p class="text-xs text-neutral-500">Скидку задайте в <a href="{{ route('admin.sales.index') }}" class="underline">«Скидки на товары»</a>.</p>
        @endif
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Размеры в наличии</label>
            <input name="available_sizes" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('available_sizes', is_array($product->available_sizes) ? implode(', ', $product->available_sizes) : '') }}" placeholder="S, M (только то, что есть на складе)">
            <p class="mt-1 text-[11px] text-neutral-500">Для одежды: XS, S, M… Для обуви: 36, 37, 38… Указывайте только размеры, которые реально есть.</p>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Остаток по размерам (точнее)</label>
            <input name="size_stock" class="w-full rounded border border-neutral-300 px-3 py-2 font-mono text-sm" value="{{ old('size_stock', is_array($product->size_stock) ? collect($product->size_stock)->map(fn ($q, $s) => $s.':'.$q)->implode(', ') : '') }}" placeholder="S:1, M:2, 42:1">
            <p class="mt-1 text-[11px] text-neutral-500">Формат S:кол-во. Если заполнено — витрина берёт данные отсюда.</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Цвет (фильтр)</label>
                <input name="color" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('color', $product->color) }}">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Пол</label>
                <select name="gender" class="w-full rounded border border-neutral-300 px-3 py-2">
                    <option value="unisex" @selected(old('gender', $product->gender ?? 'unisex') === 'unisex')>Унисекс</option>
                    <option value="female" @selected(old('gender', $product->gender) === 'female')>Женский</option>
                    <option value="male" @selected(old('gender', $product->gender) === 'male')>Мужской</option>
                </select>
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
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">или загрузите новый файл (основное)</label>
            <input type="file" name="image_file" accept="image/*" class="w-full rounded border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">URL hover</label>
            <input name="secondary_image" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('secondary_image', $product->secondary_image) }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">или загрузите новый файл (hover)</label>
            <input type="file" name="secondary_image_file" accept="image/*" class="w-full rounded border border-neutral-300 px-3 py-2">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Галерея (URL, по одному на строку)</label>
            <textarea name="gallery_images" rows="4" class="w-full rounded border border-neutral-300 px-3 py-2 font-mono text-xs" placeholder="https://...">{{ old('gallery_images', is_array($product->gallery_images) ? implode("\n", $product->gallery_images) : '') }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Описание</label>
            <textarea name="description" rows="4" class="w-full rounded border border-neutral-300 px-3 py-2">{{ old('description', $product->description) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Состав</label>
            <textarea name="composition" rows="3" class="w-full rounded border border-neutral-300 px-3 py-2" placeholder="Напр.: 95% хлопок, 5% эластан">{{ old('composition', $product->composition) }}</textarea>
        </div>
        <div class="flex gap-4 text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_new_collection" value="1" @checked(old('is_new_collection', $product->is_new_collection))> Новинка</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_limited_edition" value="1" @checked(old('is_limited_edition', $product->is_limited_edition))> Лимитированная серия</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Активен</label>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="rounded bg-black px-6 py-3 text-sm font-medium uppercase tracking-wider text-white">Сохранить</button>
            <a href="{{ route('admin.products.index') }}" class="rounded border border-neutral-300 px-6 py-3 text-sm">Назад</a>
        </div>
    </form>
@endsection
