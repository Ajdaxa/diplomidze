@extends('layouts.app')

@section('content')
    <section class="mb-6 sm:mb-8">
        <h1 class="text-[clamp(1.375rem,3.5vw,1.5rem)] font-semibold tracking-wide">Избранное</h1>
        <p class="mt-1 max-w-prose text-sm text-neutral-500 sm:text-[0.9375rem]">Ваши сохраненные товары в аккаунте.</p>
    </section>

    @if($products->isEmpty())
        <x-empty-state
            icon="heart"
            title="Избранное пусто"
            description="Нажмите на сердечко в каталоге или на странице товара — сохранённые модели будут здесь."
        >
            <a href="{{ route('catalog') }}" class="inline-flex min-h-11 items-center justify-center bg-black px-6 text-xs font-semibold uppercase tracking-[0.2em] text-white hover:bg-neutral-800">Смотреть каталог</a>
        </x-empty-state>
    @else
        <div class="grid grid-cols-2 gap-x-[clamp(0.75rem,2vw,1.25rem)] gap-y-8 min-[520px]:grid-cols-[repeat(auto-fill,minmax(11rem,1fr))] xl:grid-cols-4 xl:gap-y-10">
            @foreach($products as $product)
            <article class="favorite-item" data-id="{{ $product->id }}">
                <a href="{{ route('products.show', $product->slug) }}" class="group block">
                    <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100">
                        <img src="{{ $product->image ?: 'https://picsum.photos/800/1067?random='.$product->id }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                    </div>
                    <h2 class="mt-3 line-clamp-2 text-xs uppercase leading-snug tracking-wide">{{ $product->name }}</h2>
                    <x-product-price :product="$product" class="mt-1" />
                </a>
            </article>
            @endforeach
        </div>
    @endif
@endsection
