@extends('layouts.admin')

@section('title', 'Категория')
@section('heading', 'Редактирование категории')

@section('content')
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mx-auto max-w-lg space-y-4">
        @csrf @method('PATCH')
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Название</label>
            <input name="name" class="w-full rounded border border-neutral-300 px-3 py-2" required value="{{ old('name', $category->name) }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Slug</label>
            <input name="slug" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('slug', $category->slug) }}">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wider">Порядок</label>
            <input name="sort_order" type="number" class="w-full rounded border border-neutral-300 px-3 py-2" value="{{ old('sort_order', $category->sort_order) }}" min="0">
        </div>
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', $category->is_active ? '1' : '0') === '1')> Активна</label>
        <div class="flex gap-3">
            <button type="submit" class="rounded bg-black px-6 py-3 text-sm font-medium text-white">Сохранить</button>
            <a href="{{ route('admin.categories.index') }}" class="rounded border border-neutral-300 px-6 py-3 text-sm">Назад</a>
        </div>
    </form>
@endsection
