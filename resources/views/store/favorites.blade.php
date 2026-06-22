@extends('layouts.app')

@section('title', 'Избранное')

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
        <div id="favorites-grid" class="grid grid-cols-2 gap-x-[clamp(0.75rem,2vw,1.25rem)] gap-y-10 min-[520px]:grid-cols-[repeat(auto-fill,minmax(11rem,1fr))] xl:grid-cols-4 xl:gap-y-12">
            @foreach($products as $product)
            <article class="favorite-item group" data-id="{{ $product->id }}">
                <div class="relative aspect-[3/4] overflow-hidden bg-neutral-100">
                    <a href="{{ route('products.show', $product->slug) }}" class="absolute inset-0 z-0" aria-label="{{ $product->name }}"></a>
                    <img src="{{ $product->image ?: asset('images/product-placeholder.svg') }}" alt="{{ $product->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.02]">
                    <button type="button"
                            class="favorite-btn absolute bottom-3 right-3 z-10 flex h-10 w-10 min-h-10 min-w-10 items-center justify-center rounded-full border border-neutral-200 bg-white/90 text-red-600 shadow-sm backdrop-blur-sm hover:bg-white"
                            data-id="{{ $product->id }}"
                            data-active="1"
                            aria-label="Убрать из избранного">
                        <svg class="heart-icon h-5 w-5" fill="currentColor" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    </button>
                    <x-product-badge :product="$product" />
                </div>
                <div class="mt-4 space-y-1">
                    <h2 class="text-xs font-normal uppercase leading-snug tracking-wide text-black">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:underline">{{ $product->name }}</a>
                    </h2>
                    <x-product-price :product="$product" />
                </div>
            </article>
            @endforeach
        </div>
    @endif

    @push('scripts')
    <script>
        document.querySelectorAll('.favorite-btn').forEach((btn) => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = Number(btn.dataset.id);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/favorites/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data.status !== 'removed') return;
                const card = btn.closest('.favorite-item');
                card?.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    card?.remove();
                    if (!document.querySelector('.favorite-item')) {
                        window.location.reload();
                    }
                }, 180);
                window.dyabNotify?.('Убрано из избранного.', 'info');
            });
        });
    </script>
    @endpush
@endsection
