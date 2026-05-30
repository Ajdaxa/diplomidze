@props(['product'])

@if($product->is_new_collection)
    <span {{ $attributes->class(['absolute left-2 top-2 bg-black px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-white']) }}>Новинка</span>
@elseif($product->is_limited_edition)
    <span {{ $attributes->class(['absolute left-2 top-2 bg-neutral-800 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-white']) }}>Лимит</span>
@endif
