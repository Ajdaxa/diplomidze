@props(['product', 'size' => 'sm'])

@php
    $lg = $size === 'lg';
@endphp

@if($product->hasSale())
    <div @class(['flex flex-wrap items-baseline gap-2', $attributes->get('class')])>
        <span @class([
            'text-neutral-400 line-through decoration-neutral-300',
            'text-sm' => ! $lg,
            'text-base sm:text-lg' => $lg,
        ])>{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
        <span @class([
            'font-bold text-rose-700',
            'text-sm' => ! $lg,
            'text-[clamp(1.375rem,3.5vw,1.75rem)]' => $lg,
        ])>{{ number_format($product->saleUnitPrice(), 0, '.', ' ') }} ₽</span>
    </div>
@else
    <p @class([
        'font-bold text-black',
        'text-sm' => ! $lg,
        'text-[clamp(1.375rem,3.5vw,1.75rem)]' => $lg,
        $attributes->get('class'),
    ])>{{ number_format($product->price, 0, '.', ' ') }} ₽</p>
@endif
