@props([
    'title',
    'description' => null,
    'icon' => 'bag',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-neutral-300 bg-gradient-to-b from-neutral-50 to-white px-6 py-12 text-center sm:px-10 sm:py-14']) }}>
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-neutral-200 bg-white shadow-sm">
        @if($icon === 'heart')
            <svg class="h-7 w-7 text-neutral-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
        @elseif($icon === 'cart')
            <svg class="h-7 w-7 text-neutral-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
            </svg>
        @else
            <svg class="h-7 w-7 text-neutral-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M5.25 7.5h13.5"/>
            </svg>
        @endif
    </div>
    <h2 class="mt-5 text-lg font-semibold tracking-tight text-neutral-900">{{ $title }}</h2>
    @if($description)
        <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-neutral-500">{{ $description }}</p>
    @endif
    @if(trim($slot))
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
