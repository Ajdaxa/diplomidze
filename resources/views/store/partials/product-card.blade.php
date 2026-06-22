<article class="group overflow-hidden rounded-xl border border-neutral-200 bg-white transition hover:border-neutral-900">
    <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-[3/4] overflow-hidden bg-neutral-100">
        <img src="{{ $product->image ?: asset('images/product-placeholder.svg') }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
        @auth
            <button type="button" class="favorite-btn absolute bottom-3 right-3 z-10 flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 bg-white/90 text-neutral-800 shadow-sm backdrop-blur-sm hover:bg-white {{ isset($favoriteLookup[$product->id]) ? 'text-red-600' : '' }}" data-id="{{ $product->id }}" data-active="{{ isset($favoriteLookup[$product->id]) ? '1' : '0' }}" aria-label="Избранное">
                <svg class="heart-icon h-5 w-5" fill="{{ isset($favoriteLookup[$product->id]) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
            </button>
        @endauth
        <x-product-badge :product="$product" />
    </a>
    <div class="p-3 sm:p-4">
        <h3 class="text-[11px] font-medium uppercase leading-snug tracking-wide text-neutral-900 sm:text-xs">
            <a href="{{ route('products.show', $product->slug) }}" class="hover:underline">{{ $product->name }}</a>
        </h3>
        <x-product-price :product="$product" class="mt-1" />
    </div>
</article>
