@props(['product'])

@php
    $base = 'absolute z-[5] px-2 py-0.5 text-[8px] font-semibold uppercase tracking-wider sm:text-[9px]';
    $hasSale = $product->hasSale();
@endphp

@if($hasSale)
    <span {{ $attributes->class([$base, 'left-2 top-2 bg-rose-600 text-white']) }}>−{{ (int) $product->sale_percent }}%</span>
@endif
@if($product->is_new_collection)
    <span @class([
        $base,
        'bg-black text-white',
        $hasSale ? 'left-2 top-9' : 'left-2 top-2',
    ])>Новинка</span>
@elseif($product->is_limited_edition)
    <span @class([
        $base,
        'bg-neutral-800 text-white',
        $hasSale ? 'left-2 top-9' : 'left-2 top-2',
    ])>Лимитировано</span>
@endif
