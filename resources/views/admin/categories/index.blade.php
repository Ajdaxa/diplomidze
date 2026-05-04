@extends('layouts.admin')

@section('title', 'Категории')
@section('heading', 'Категории')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.categories.create') }}" class="inline-block rounded bg-black px-4 py-2 text-sm font-medium text-white">Новая категория</a>
    </div>
    <div class="space-y-3">
        @foreach($categories as $category)
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-stone-200 bg-white p-4">
                <div>
                    <p class="font-medium">{{ $category->name }}</p>
                    <p class="text-sm text-stone-500">{{ $category->slug }} · порядок {{ $category->sort_order }} · {{ $category->is_active ? 'активна' : 'скрыта' }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="rounded border border-stone-300 px-3 py-2 text-sm">Изменить</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Удалить категорию?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="rounded border border-rose-300 px-3 py-2 text-sm text-rose-700">Удалить</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $categories->links() }}</div>
@endsection
