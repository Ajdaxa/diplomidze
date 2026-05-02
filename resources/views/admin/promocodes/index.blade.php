@extends('layouts.app')

@section('content')
    <div class="mb-6 rounded-xl border border-stone-200 bg-white p-4">
        <h1 class="mb-4 text-2xl font-semibold">Промокоды</h1>
        <form method="POST" action="{{ route('admin.promocodes.store') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
            @csrf
            <input name="code" placeholder="Код" class="rounded border border-stone-300 px-3 py-2" required>
            <select name="type" class="rounded border border-stone-300 px-3 py-2">
                <option value="percent">Процент</option>
                <option value="fixed">Фикс</option>
            </select>
            <input name="value" type="number" placeholder="Значение" class="rounded border border-stone-300 px-3 py-2" required>
            <button class="rounded bg-stone-900 px-4 py-2 text-white">Создать</button>
        </form>
    </div>
    <div class="space-y-3">
        @foreach($promocodes as $promocode)
            <div class="flex items-center justify-between rounded-xl border border-stone-200 bg-white p-4">
                <div>
                    <p class="font-medium">{{ $promocode->code }}</p>
                    <p class="text-sm text-stone-500">{{ $promocode->usage_count }} использований</p>
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.promocodes.update', $promocode) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="value" value="{{ $promocode->value }}" class="w-24 rounded border border-stone-300 px-2 py-1 text-sm">
                        <label class="text-xs"><input type="checkbox" name="is_active" value="1" @checked($promocode->is_active)> active</label>
                        <button class="rounded border border-stone-300 px-3 py-2 text-sm">Сохранить</button>
                    </form>
                    <form method="POST" action="{{ route('admin.promocodes.broadcast', $promocode) }}">
                        @csrf
                        <button class="rounded border border-stone-300 px-3 py-2 text-sm">Разослать промокод всем</button>
                    </form>
                    <form method="POST" action="{{ route('admin.promocodes.destroy', $promocode) }}">
                        @csrf @method('DELETE')
                        <button class="rounded border border-rose-300 px-3 py-2 text-sm text-rose-700">Удалить</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
