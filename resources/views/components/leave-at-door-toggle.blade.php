@props([
    'checked' => false,
    'name' => 'leave_at_door',
])

@php
    $isOn = (bool) old($name, $checked);
@endphp

<label
    for="leave-at-door-input"
    class="leave-at-door-card group relative flex cursor-pointer items-start gap-4 rounded-2xl border p-4 transition-all duration-300 ease-out sm:items-center sm:p-5 {{ $isOn ? 'border-emerald-200 bg-gradient-to-br from-emerald-50/90 via-white to-white shadow-[0_1px_0_rgba(16,185,129,0.08)]' : 'border-neutral-200 bg-neutral-50/60 hover:border-neutral-300 hover:bg-neutral-50' }}"
>
    <input type="hidden" name="{{ $name }}" value="0">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="leave-at-door-input"
        value="1"
        class="peer sr-only"
        @checked($isOn)
    >

    <span class="leave-at-door-icon flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border transition-colors duration-300 {{ $isOn ? 'border-emerald-200 bg-white text-emerald-700' : 'border-neutral-200 bg-white text-neutral-500 group-hover:text-neutral-700' }}">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 12h2.5a1.5 1.5 0 0 1 1.5 1.5V15" />
        </svg>
    </span>

    <span class="min-w-0 flex-1">
        <span class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-semibold tracking-tight text-neutral-900">Оставить у двери</span>
            <span class="leave-at-door-pill rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-emerald-800 {{ $isOn ? 'inline-flex' : 'hidden' }}">Включено</span>
        </span>
        <span class="mt-1 block text-xs leading-relaxed text-neutral-500">
            Курьер оставит заказ у двери и приложит фото подтверждения доставки
        </span>
    </span>

    <span class="leave-at-door-track relative ml-auto h-7 w-12 shrink-0 rounded-full border transition-colors duration-300 ease-out {{ $isOn ? 'border-emerald-300 bg-emerald-500' : 'border-neutral-300 bg-neutral-200' }}" aria-hidden="true">
        <span class="leave-at-door-thumb absolute left-0.5 top-0.5 h-6 w-6 rounded-full bg-white shadow-sm transition-transform duration-300 ease-out {{ $isOn ? 'translate-x-5' : 'translate-x-0' }}"></span>
    </span>
</label>

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('leave-at-door-input');
        const card = document.querySelector('.leave-at-door-card');
        if (!input || !card) return;

        const pill = card.querySelector('.leave-at-door-pill');
        const track = card.querySelector('.leave-at-door-track');
        const thumb = card.querySelector('.leave-at-door-thumb');
        const icon = card.querySelector('.leave-at-door-icon');

        const sync = () => {
            const on = input.checked;

            card.classList.toggle('border-emerald-200', on);
            card.classList.toggle('bg-gradient-to-br', on);
            card.classList.toggle('from-emerald-50/90', on);
            card.classList.toggle('via-white', on);
            card.classList.toggle('to-white', on);
            card.classList.toggle('shadow-[0_1px_0_rgba(16,185,129,0.08)]', on);
            card.classList.toggle('border-neutral-200', !on);
            card.classList.toggle('bg-neutral-50/60', !on);

            pill?.classList.toggle('hidden', !on);
            pill?.classList.toggle('inline-flex', on);

            track?.classList.toggle('border-emerald-300', on);
            track?.classList.toggle('bg-emerald-500', on);
            track?.classList.toggle('border-neutral-300', !on);
            track?.classList.toggle('bg-neutral-200', !on);

            thumb?.classList.toggle('translate-x-5', on);
            thumb?.classList.toggle('translate-x-0', !on);

            icon?.classList.toggle('border-emerald-200', on);
            icon?.classList.toggle('text-emerald-700', on);
            icon?.classList.toggle('border-neutral-200', !on);
            icon?.classList.toggle('text-neutral-500', !on);
        };

        input.addEventListener('change', sync);
    })();
</script>
@endpush
