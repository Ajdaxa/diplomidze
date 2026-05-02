@extends('layouts.app')

@section('content')
    <section class="mb-8">
        <h1 class="text-2xl font-semibold tracking-wide">Избранное</h1>
        <p class="mt-1 text-sm text-neutral-500">Ваши сохраненные товары в аккаунте.</p>
    </section>

    @if($products->isEmpty())
        <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-8 text-center">
            <p class="text-lg font-medium">Пока пусто</p>
            <p class="mt-2 text-sm text-neutral-500">Добавляйте товары в избранное на витрине и в карточке товара.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block rounded-lg bg-black px-4 py-2 text-sm text-white">Перейти на витрину</a>
        </div>
    @else
        <div class="grid grid-cols-2 gap-x-4 gap-y-10 md:grid-cols-3 lg:grid-cols-4">
            @foreach($products as $product)
            <article class="favorite-item" data-id="{{ $product->id }}">
                <a href="{{ route('products.show', $product->slug) }}" class="group block">
                    <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100">
                        <img src="{{ $product->image ?: 'https://picsum.photos/800/1067?random='.$product->id }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                    </div>
                    <h2 class="mt-3 text-xs uppercase tracking-wide">{{ $product->name }}</h2>
                    <p class="mt-1 text-sm font-bold">{{ number_format($product->price, 0, '.', ' ') }} ₽</p>
                </a>
            </article>
            @endforeach
        </div>
    @endif
@endsection
