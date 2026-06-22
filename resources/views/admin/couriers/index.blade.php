@extends('layouts.admin')

@section('title', 'Курьеры')
@section('heading', 'Курьеры')

@section('content')
    @if($isAdmin)
        <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4">
            <form method="POST" action="{{ route('admin.couriers.store') }}" class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @csrf
                <input name="name" placeholder="Имя курьера" class="rounded border border-stone-300 px-3 py-2" required>
                <input name="phone" placeholder="Телефон" class="rounded border border-stone-300 px-3 py-2" required>
                <input name="email" placeholder="Email (опционально)" class="rounded border border-stone-300 px-3 py-2">
                <input name="password" type="password" placeholder="Пароль для входа" class="rounded border border-stone-300 px-3 py-2" required>
                <input name="courier_commission_percent" type="number" step="0.01" min="0" max="100" value="10" placeholder="Комиссия, %" class="rounded border border-stone-300 px-3 py-2" required>
                <button class="md:col-span-2 rounded bg-stone-900 px-4 py-2 text-white">Создать курьера</button>
            </form>
        </div>
    @else
        <p class="mb-6 text-sm text-neutral-600">Список курьеров для назначения на заказы. Редактирование доступно только администратору.</p>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach($couriers as $courier)
            <div class="rounded-xl border border-stone-200 bg-white p-4">
                @if($isAdmin)
                    <form method="POST" action="{{ route('admin.couriers.update', $courier) }}" class="space-y-2">
                        @csrf @method('PATCH')
                        <input name="name" value="{{ $courier->name }}" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">
                        <input name="phone" value="{{ $courier->phone }}" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">
                        <input name="email" value="{{ $courier->email }}" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">
                        <input name="password" type="password" placeholder="Новый пароль (опц.)" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">
                        <input name="courier_commission_percent" type="number" step="0.01" min="0" max="100" value="{{ $courier->courier_commission_percent ?? 10 }}" placeholder="Комиссия, %" class="w-full rounded border border-stone-300 px-2 py-1 text-sm">
                        <button class="rounded border border-stone-300 px-3 py-1 text-sm">Сохранить</button>
                    </form>
                    <form method="POST" action="{{ route('admin.couriers.destroy', $courier) }}" class="mt-2">
                        @csrf @method('DELETE')
                        <button class="rounded border border-rose-300 px-3 py-1 text-sm text-rose-700">Удалить</button>
                    </form>
                @else
                    <h3 class="font-medium text-neutral-900">{{ $courier->name }}</h3>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div><dt class="text-xs uppercase tracking-wider text-neutral-500">Телефон</dt><dd>{{ $courier->phone ?: '—' }}</dd></div>
                        <div><dt class="text-xs uppercase tracking-wider text-neutral-500">Email</dt><dd>{{ $courier->email ?: '—' }}</dd></div>
                        <div><dt class="text-xs uppercase tracking-wider text-neutral-500">Комиссия</dt><dd>{{ number_format($courier->courier_commission_percent ?? 10, 0, '.', ' ') }}%</dd></div>
                    </dl>
                @endif
            </div>
        @endforeach
    </div>
@endsection
