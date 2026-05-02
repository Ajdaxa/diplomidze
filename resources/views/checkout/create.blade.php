@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl">
        <h1 class="mb-2 text-3xl font-light uppercase tracking-wide">Оформлениe</h1>
        <p class="mb-10 text-sm text-neutral-500">Доставка и оплата через YooMoney</p>

        <div class="grid gap-8 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <div class="border border-neutral-200 bg-white p-6">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500">Ваш заказ</h2>
                    <ul class="mt-6 space-y-4">
                        @forelse($items as $item)
                            <li class="flex justify-between gap-4 border-b border-neutral-100 pb-4 text-sm last:border-0">
                                <div>
                                    <p class="font-medium uppercase tracking-wide">{{ $item['product']->name }}</p>
                                    <p class="mt-1 text-xs text-neutral-500">{{ $item['size'] }} × {{ $item['quantity'] }}</p>
                                </div>
                                <p class="shrink-0 font-semibold">{{ number_format($item['line_total'], 0, '.', ' ') }} ₽</p>
                            </li>
                        @empty
                            <li class="text-sm text-neutral-500">Корзина пуста.</li>
                        @endforelse
                    </ul>
                    <div class="mt-6 flex justify-between border-t border-neutral-200 pt-6 text-base font-bold">
                        <span>Итого</span>
                        <span>{{ number_format($cartTotal, 0, '.', ' ') }} ₽</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6 border border-neutral-200 bg-white p-6 lg:p-8">
                    @csrf
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-neutral-700">Адрес доставки</label>
                        <input id="address-input" type="text" autocomplete="off" class="w-full border border-neutral-300 px-4 py-3 text-sm focus:border-black focus:outline-none" placeholder="Начните вводить адрес">
                        <input type="hidden" name="address[full]" id="address-full">
                        <div id="address-suggestions" class="mt-2 max-h-48 space-y-1 overflow-y-auto"></div>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-neutral-700">Промокод</label>
                        <input type="text" name="promocode" class="w-full border border-neutral-300 px-4 py-3 text-sm focus:border-black focus:outline-none" placeholder="Необязательно">
                    </div>
                    <button type="submit" class="w-full bg-black py-4 text-xs font-semibold uppercase tracking-[0.25em] text-white transition hover:bg-neutral-800">Перейти к оплате</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const addressInput = document.getElementById('address-input');
        const hiddenAddress = document.getElementById('address-full');
        const suggestionsEl = document.getElementById('address-suggestions');
        let timer;
        addressInput?.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(async () => {
                const query = addressInput.value.trim();
                if (query.length < 3) {
                    suggestionsEl.innerHTML = '';
                    return;
                }
                const url = new URL('{{ route('checkout.address.suggestions') }}');
                url.searchParams.set('query', query);
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                const items = await res.json();
                suggestionsEl.innerHTML = items.map((item) =>
                    `<button type="button" class="block w-full border-b border-neutral-100 px-3 py-2.5 text-left text-sm hover:bg-neutral-50" data-value="${item.value.replace(/"/g, '&quot;')}">${item.value}</button>`
                ).join('');
                suggestionsEl.querySelectorAll('button').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        addressInput.value = btn.dataset.value;
                        hiddenAddress.value = btn.dataset.value;
                        suggestionsEl.innerHTML = '';
                    });
                });
            }, 280);
        });
    </script>
    @endpush
@endsection
