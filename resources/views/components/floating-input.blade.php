@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'id' => null,
    'autocomplete' => null,
    'required' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'inputmode' => null,
])

@php
    $fieldId = $id ?? $name;
    $raw = $value !== null ? $value : old($name);
    $displayValue = $type === 'password' ? '' : (string) ($raw ?? '');
    $err = $errors->has($name);
@endphp

<div class="relative">
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $fieldId }}"
        value="{{ $displayValue }}"
        placeholder=" "
        @if($required) required @endif
        @if($autocomplete !== null && $autocomplete !== '') autocomplete="{{ $autocomplete }}" @endif
        @if($inputmode) inputmode="{{ $inputmode }}" @endif
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
        @if($step !== null) step="{{ $step }}" @endif
        class="peer block w-full rounded-xl border bg-white px-3.5 pb-2.5 pt-5 text-sm text-neutral-900 shadow-sm outline-none transition-[border-color,box-shadow,background-color] duration-200 ease-out placeholder:text-transparent focus:ring-2 focus:ring-black/[0.06] {{ $err ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10' : 'border-neutral-200 focus:border-black' }}"
    />
    <label
        for="{{ $fieldId }}"
        class="pointer-events-none absolute left-3.5 top-1/2 origin-left -translate-y-1/2 text-[15px] text-neutral-500 transition-all duration-200 ease-out peer-focus:top-2 peer-focus:translate-y-0 peer-focus:text-[11px] peer-focus:font-medium peer-focus:tracking-wide peer-focus:text-neutral-700 peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:translate-y-0 peer-[:not(:placeholder-shown)]:text-[11px] peer-[:not(:placeholder-shown)]:font-medium peer-[:not(:placeholder-shown)]:tracking-wide peer-[:not(:placeholder-shown)]:text-neutral-700"
    >{{ $label }}</label>
    @error($name)
        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
