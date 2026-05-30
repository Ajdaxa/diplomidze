@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
    <div class="mx-auto w-full max-w-5xl">
        <x-page-heading title="Оформление" lede="Доставка и оплата через YooMoney" />

        <div class="grid gap-8 lg:grid-cols-5 lg:gap-10">
            <div class="order-2 lg:order-1 lg:col-span-2">
                <div class="border border-neutral-200 bg-white p-4 sm:p-6 lg:sticky lg:top-20">
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
                    <div class="mt-6 space-y-2 border-t border-neutral-200 pt-6 text-sm">
                        <div class="flex justify-between text-neutral-600">
                            <span>Товары</span>
                            <span id="checkout-subtotal">{{ number_format($cartTotal, 0, '.', ' ') }} ₽</span>
                        </div>
                        <div id="checkout-discount-row" class="hidden flex justify-between text-emerald-700">
                            <span>Скидка</span>
                            <span id="checkout-discount">—</span>
                        </div>
                        <div class="flex justify-between border-t border-neutral-200 pt-4 text-base font-bold">
                            <span>К оплате</span>
                            <span id="checkout-total">{{ number_format($cartTotal, 0, '.', ' ') }} ₽</span>
                        </div>
                    </div>
                    <p id="checkout-promo-hint" class="mt-3 hidden text-xs text-neutral-500"></p>
                </div>
            </div>

            <div class="order-1 min-w-0 lg:order-2 lg:col-span-3">
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6 border border-neutral-200 bg-white p-4 sm:p-6 lg:p-8">
                    @csrf
                    <div>
                        <div class="relative">
                            <input id="address-input" type="text" autocomplete="off" placeholder=" " class="peer block w-full rounded-xl border border-neutral-200 bg-white px-3.5 pb-2.5 pt-5 text-sm text-neutral-900 shadow-sm outline-none transition-[border-color,box-shadow] duration-200 ease-out placeholder:text-transparent focus:border-black focus:ring-2 focus:ring-black/[0.06]">
                            <label for="address-input" class="pointer-events-none absolute left-3.5 top-1/2 origin-left -translate-y-1/2 text-[15px] text-neutral-500 transition-all duration-200 ease-out peer-focus:top-2 peer-focus:translate-y-0 peer-focus:text-[11px] peer-focus:font-medium peer-focus:tracking-wide peer-focus:text-neutral-700 peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:translate-y-0 peer-[:not(:placeholder-shown)]:text-[11px] peer-[:not(:placeholder-shown)]:font-medium peer-[:not(:placeholder-shown)]:tracking-wide peer-[:not(:placeholder-shown)]:text-neutral-700">Адрес доставки</label>
                        </div>
                        <input type="hidden" name="address[full]" id="address-full">
                        <div id="address-suggestions" class="mt-2 max-h-48 space-y-1 overflow-y-auto"></div>
                    </div>
                    <x-floating-input id="checkout-promocode" name="promocode" label="Промокод (необязательно)" :value="old('promocode')" autocomplete="off" />
                    <button type="submit" class="w-full rounded-xl bg-black py-4 text-xs font-semibold uppercase tracking-[0.25em] text-white transition hover:bg-neutral-800">Перейти к оплате</button>
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

        (function () {
            const fmtRub = (n) => {
                const raw = typeof n === 'number' ? n : parseFloat(String(n ?? '').replace(/\s/g, '').replace(',', '.'));
                if (!Number.isFinite(raw)) {
                    return '—';
                }
                return new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 0 }).format(Math.round(raw)) + ' ₽';
            };
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const promoInput = document.getElementById('checkout-promocode');
            const hint = document.getElementById('checkout-promo-hint');
            const subEl = document.getElementById('checkout-subtotal');
            const discRow = document.getElementById('checkout-discount-row');
            const discEl = document.getElementById('checkout-discount');
            const totEl = document.getElementById('checkout-total');
            let t;
            async function preview() {
                hint?.classList.add('hidden');
                const params = new URLSearchParams();
                params.set('_token', csrf || '');
                params.set('promocode', promoInput?.value?.trim() || '');
                const res = await fetch('{{ route('cart.preview-totals') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-CSRF-TOKEN': csrf || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: params.toString(),
                });
                let data = {};
                try {
                    data = await res.json();
                } catch (e) {
                    return;
                }
                if (!res.ok || typeof data.subtotal !== 'number') {
                    return;
                }
                if (data.empty_cart) return;
                subEl.textContent = fmtRub(data.subtotal);
                if (Number(data.discount) > 0 && data.promocode?.valid) {
                    discRow?.classList.remove('hidden');
                    discEl.textContent = '− ' + fmtRub(data.discount);
                } else {
                    discRow?.classList.add('hidden');
                }
                totEl.textContent = fmtRub(data.total);
                if (data.promocode?.message && promoInput?.value?.trim()) {
                    hint.textContent = data.promocode.message;
                    hint.classList.remove('hidden');
                    hint.classList.toggle('text-rose-600', !data.promocode.valid);
                    hint.classList.toggle('text-emerald-700', data.promocode.valid);
                }
            }
            promoInput?.addEventListener('input', () => {
                clearTimeout(t);
                t = setTimeout(preview, 320);
            });
            preview();
        })();
    </script>
    @endpush
@endsection
