@props(['title', 'lede' => null])

<div {{ $attributes->class(['mb-8 sm:mb-10']) }}>
    <h1 class="text-[clamp(1.5rem,4vw,1.875rem)] font-light uppercase tracking-wide text-neutral-900">{{ $title }}</h1>
    @if($lede)
        <p class="mt-2 max-w-prose text-sm leading-relaxed text-neutral-500 sm:text-[0.9375rem]">{{ $lede }}</p>
    @endif
    {{ $slot ?? '' }}
</div>
