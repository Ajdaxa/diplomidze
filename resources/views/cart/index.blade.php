@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="mb-2 text-3xl font-light uppercase tracking-wide">Корзина</h1>
        <p class="mb-10 text-sm text-neutral-500">Проверьте размеры и количество перед оформлением</p>

        <div class="divide-y divide-neutral-200 border border-neutral-200 bg-white">
            @forelse($items as $item)
                <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex gap-4">
                        <div class="h-24 w-20 shrink-0 bg-neutral-100">
                            @if($item['product']->image)
                                <img src="{{ $item['product']->image }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide">{{ $item['product']->name }}</p>
                            <p class="mt-1 text-xs text-neutral-500">Размер: <span class="font-medium text-black">{{ $item['size'] }}</span></p>
                            <p class="text-sm font-bold">{{ number_format($item['product']->price, 0, '.', ' ') }} ₽</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="hidden" name="size" value="{{ $item['size'] }}">
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="20" class="w-16 border border-neutral-300 px-2 py-1 text-sm">
                            <button type="submit" class="text-xs uppercase tracking-wider text-neutral-600 underline">Обновить</button>
                        </form>
                        <form method="POST" action="{{ route('cart.remove', $item['product']) }}">
                            @csrf @method('DELETE')
                            <input type="hidden" name="size" value="{{ $item['size'] }}">
                            <button type="submit" class="text-xs text-red-600 hover:underline">Удалить</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-sm text-neutral-500">Корзина пуста. Перейдите в <a href="{{ route('home') }}" class="underline">витрину</a>.</div>
            @endforelse
        </div>

        @if($items->isNotEmpty())
            <div class="mt-8 flex flex-col gap-4 border border-neutral-200 bg-neutral-50 p-6 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-lg font-semibold">Итого: {{ number_format($total, 0, '.', ' ') }} ₽</p>
                @auth
                    <a href="{{ route('checkout.create') }}" class="inline-block bg-black px-8 py-3 text-center text-xs font-semibold uppercase tracking-[0.2em] text-white hover:bg-neutral-800">Оформить заказ</a>
                @else
                    <a href="{{ route('otp.form') }}" class="inline-block border border-black px-8 py-3 text-center text-xs font-semibold uppercase tracking-[0.2em]">Войти для оформления</a>
                @endauth
            </div>
        @endif
    </div>
@endsection
