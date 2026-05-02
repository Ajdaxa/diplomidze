@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">Корзина</h1>
    <div class="space-y-4">
        @forelse($items as $item)
            <div class="flex items-center justify-between rounded-xl border border-stone-200 bg-white p-4">
                <div>
                    <p class="font-medium">{{ $item['product']->name }}</p>
                    <p class="text-sm text-stone-500">{{ number_format($item['product']->price, 2, '.', ' ') }} ₽</p>
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-20 rounded border border-stone-300 px-2 py-1">
                        <button class="rounded border border-stone-300 px-3 py-1 text-sm">Обновить</button>
                    </form>
                    <form method="POST" action="{{ route('cart.remove', $item['product']) }}">
                        @csrf @method('DELETE')
                        <button class="rounded border border-rose-300 px-3 py-1 text-sm text-rose-700">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-stone-500">Корзина пуста.</p>
        @endforelse
    </div>
    <div class="mt-6 rounded-xl border border-stone-200 bg-white p-4">
        <p class="text-lg font-medium">Итого: {{ number_format($total, 2, '.', ' ') }} ₽</p>
        <a href="{{ route('checkout.create') }}" class="mt-3 inline-block rounded bg-stone-900 px-4 py-2 text-white">Перейти к оформлению</a>
    </div>
@endsection
