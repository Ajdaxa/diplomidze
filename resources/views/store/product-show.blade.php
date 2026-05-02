@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-stone-200 bg-white p-2">
            <img src="{{ $product->image ?: 'https://picsum.photos/1000/1200?random='.$product->id }}" class="h-[540px] w-full rounded-lg object-cover" alt="{{ $product->name }}">
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h1 class="text-3xl font-semibold">{{ $product->name }}</h1>
            <p class="mt-2 text-stone-600">{{ $product->description }}</p>
            <p class="mt-4 text-2xl font-semibold">{{ number_format($product->price, 2, '.', ' ') }} ₽</p>
            <p class="mt-2 text-sm text-stone-500">Размер: {{ $product->size ?: 'Уточняется' }} • Цвет: {{ $product->color ?: 'Уточняется' }}</p>
            <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-6">
                @csrf
                <button class="rounded-lg bg-stone-900 px-5 py-3 text-white">Добавить в корзину</button>
            </form>
        </div>
    </div>
@endsection
