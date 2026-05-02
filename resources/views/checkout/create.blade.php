@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl rounded-xl border border-stone-200 bg-white p-6">
        <h1 class="mb-4 text-2xl font-semibold">Оформление заказа</h1>
        <form method="POST" action="{{ route('checkout.store') }}" class="space-y-4">
            @csrf
            <div class="rounded-lg bg-stone-50 p-3 text-sm">
                <p class="font-medium">Состав заказа</p>
                @forelse($items as $item)
                    <p>{{ $item['product']->name }} x {{ $item['quantity'] }} — {{ number_format($item['line_total'], 2, '.', ' ') }} ₽</p>
                @empty
                    <p>Корзина пуста.</p>
                @endforelse
                <p class="mt-2 font-semibold">Итого: {{ number_format($cartTotal, 2, '.', ' ') }} ₽</p>
            </div>
            <div>
                <label class="mb-1 block text-sm">Адрес</label>
                <input id="address-input" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                <input type="hidden" name="address[full]" id="address-full">
                <div id="address-suggestions" class="mt-2 space-y-1"></div>
            </div>
            <div>
                <label class="mb-1 block text-sm">Промокод</label>
                <input type="text" name="promocode" class="w-full rounded-lg border border-stone-300 px-3 py-2">
            </div>
            <button class="rounded-lg bg-stone-900 px-4 py-2 text-white">Перейти к оплате</button>
        </form>
    </div>
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
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const items = await res.json();
                suggestionsEl.innerHTML = items.map((item) =>
                    `<button type="button" class="block w-full rounded border border-stone-200 px-3 py-2 text-left hover:bg-stone-100" data-value="${item.value}">${item.value}</button>`
                ).join('');
                suggestionsEl.querySelectorAll('button').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        addressInput.value = btn.dataset.value;
                        hiddenAddress.value = btn.dataset.value;
                        suggestionsEl.innerHTML = '';
                    });
                });
            }, 250);
        });
    </script>
@endsection
