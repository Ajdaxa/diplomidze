@extends('layouts.app')

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        <h1 class="mb-2 text-[clamp(1.5rem,4vw,1.875rem)] font-light uppercase tracking-wide">Корзина</h1>
        <p class="mb-8 max-w-prose text-sm leading-relaxed text-neutral-500 sm:mb-10 sm:text-[0.9375rem]">Проверьте размеры и количество перед оформлением</p>

        <div class="divide-y divide-neutral-200 border border-neutral-200 bg-white">
            @forelse($items as $item)
                <div class="flex min-w-0 flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <div class="flex min-w-0 gap-3 sm:gap-4">
                        <div class="h-24 w-20 shrink-0 bg-neutral-100 sm:h-28 sm:w-24">
                            @if($item['product']->image)
                                <img src="{{ $item['product']->image }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold uppercase leading-snug tracking-wide">{{ $item['product']->name }}</p>
                            <p class="mt-1 text-xs text-neutral-500">Размер: <span class="font-medium text-black">{{ $item['size'] }}</span></p>
                            <p class="text-sm font-bold">{{ number_format($item['product']->price, 0, '.', ' ') }} ₽</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 sm:shrink-0">
                        <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex flex-wrap items-center gap-2">
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
                <div class="p-12 text-center text-sm text-neutral-500">Корзина пуста. Загляните в <a href="{{ route('catalog') }}" class="underline">каталог</a>.</div>
            @endforelse
        </div>

        @if($items->isNotEmpty())
            <div class="mt-8 space-y-4 border border-neutral-200 bg-neutral-50 p-4 sm:p-6">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-neutral-600">
                        <span>Товары</span>
                        <span>{{ number_format($total, 0, '.', ' ') }} ₽</span>
                    </div>
                    <div class="flex justify-between text-base font-semibold text-neutral-900">
                        <span>К оплате</span>
                        <span>{{ number_format($total, 0, '.', ' ') }} ₽</span>
                    </div>
                </div>
                <div class="flex flex-col gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
                    @auth
                        <a href="{{ route('checkout.create') }}" class="inline-flex min-h-12 w-full items-center justify-center bg-black px-6 py-3 text-center text-xs font-semibold uppercase tracking-[0.2em] text-white hover:bg-neutral-800 sm:order-2 sm:w-auto sm:px-8">Оформить заказ</a>
                    @else
                        <a href="{{ route('otp.form') }}" class="inline-flex min-h-12 w-full items-center justify-center border border-black px-6 py-3 text-center text-xs font-semibold uppercase tracking-[0.2em] sm:order-2 sm:w-auto sm:px-8">Войти для оформления</a>
                    @endauth
                </div>
            </div>
        @endif
    </div>
@endsection
