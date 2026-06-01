@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
    <div class="mx-auto w-full max-w-5xl">
        <x-page-heading title="Оформление" lede="Доставка и оплата через YooMoney" />

        @if($items->isEmpty())
            <x-empty-state icon="cart" title="Нечего оформлять" description="Сначала добавьте товары в корзину.">
                <a href="{{ route('catalog') }}" class="inline-flex min-h-11 items-center justify-center bg-black px-6 text-xs font-semibold uppercase tracking-[0.2em] text-white">В каталог</a>
            </x-empty-state>
        @else
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
                                <div class="shrink-0 text-right">
                                    @if($item['product_discount'] > 0)
                                        <p class="text-xs text-neutral-400 line-through">{{ number_format($item['line_original_total'], 0, '.', ' ') }} ₽</p>
                                    @endif
                                    <p class="font-semibold">{{ number_format($item['line_total'], 0, '.', ' ') }} ₽</p>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-neutral-500">Корзина пуста.</li>
                        @endforelse
                    </ul>
                    <div class="mt-6 space-y-2 border-t border-neutral-200 pt-6 text-sm">
                        <div class="flex justify-between text-neutral-600">
                            <span>Товары</span>
                            <span id="checkout-catalog-subtotal">{{ number_format($catalogSubtotal, 0, '.', ' ') }} ₽</span>
                        </div>
                        <div id="checkout-product-discount-row" class="{{ $productDiscount > 0 ? 'flex' : 'hidden' }} justify-between text-rose-700">
                            <span>Скидка на товары</span>
                            <span id="checkout-product-discount">− {{ number_format($productDiscount, 0, '.', ' ') }} ₽</span>
                        </div>
                        <div id="checkout-promo-discount-row" class="hidden justify-between text-emerald-700">
                            <span>Промокод</span>
                            <span id="checkout-promo-discount">—</span>
                        </div>
                        <div id="checkout-loyalty-discount-row" class="hidden justify-between text-sky-700">
                            <span>Баллы лояльности</span>
                            <span id="checkout-loyalty-discount">—</span>
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
                    @if($loyaltyPoints > 0 && $maxLoyaltySpend > 0)
                        <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Баллы</p>
                            <p class="mt-1 text-sm text-neutral-700">На счёте <strong>{{ number_format($loyaltyPoints, 0, '.', ' ') }}</strong> баллов. Можно списать до <strong>{{ number_format($maxLoyaltySpend, 0, '.', ' ') }} ₽</strong> (30% от суммы заказа).</p>
                            <label class="mt-4 flex cursor-pointer items-center gap-3">
                                <input type="checkbox" name="spend_loyalty" value="1" id="checkout-spend-loyalty" class="h-4 w-4 rounded border-neutral-400" @checked(old('spend_loyalty'))>
                                <span class="text-sm font-medium text-neutral-900">Списать баллы</span>
                            </label>
                        </div>
                    @endif
                    <div class="space-y-3 rounded-xl border border-neutral-200 bg-neutral-50/80 p-4 text-xs leading-relaxed text-neutral-600">
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="accept_offer" value="1" required class="mt-0.5 rounded border-neutral-400" @checked(old('accept_offer'))>
                            <span>Я принимаю условия <a href="{{ route('pages.offer') }}" target="_blank" class="font-medium text-neutral-900 underline">публичной оферты</a></span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="accept_privacy" value="1" required class="mt-0.5 rounded border-neutral-400" @checked(old('accept_privacy'))>
                            <span>Я согласен с <a href="{{ route('pages.privacy') }}" target="_blank" class="font-medium text-neutral-900 underline">политикой конфиденциальности</a></span>
                        </label>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-black py-4 text-xs font-semibold uppercase tracking-[0.25em] text-white transition hover:bg-neutral-800">Перейти к оплате</button>
                </form>
            </div>
        </div>
        @endif
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
            const catalogEl = document.getElementById('checkout-catalog-subtotal');
            const productDiscRow = document.getElementById('checkout-product-discount-row');
            const productDiscEl = document.getElementById('checkout-product-discount');
            const promoDiscRow = document.getElementById('checkout-promo-discount-row');
            const promoDiscEl = document.getElementById('checkout-promo-discount');
            const loyaltyDiscRow = document.getElementById('checkout-loyalty-discount-row');
            const loyaltyDiscEl = document.getElementById('checkout-loyalty-discount');
            const loyaltyCheckbox = document.getElementById('checkout-spend-loyalty');
            const totEl = document.getElementById('checkout-total');
            let t;
            async function preview() {
                hint?.classList.add('hidden');
                const params = new URLSearchParams();
                params.set('_token', csrf || '');
                params.set('promocode', promoInput?.value?.trim() || '');
                if (loyaltyCheckbox?.checked) {
                    params.set('spend_loyalty', '1');
                }
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
                if (!res.ok || typeof data.total !== 'number') {
                    return;
                }
                if (data.empty_cart) return;
                catalogEl.textContent = fmtRub(data.catalog_subtotal ?? data.subtotal);
                if (Number(data.product_discount) > 0) {
                    productDiscRow?.classList.remove('hidden');
                    productDiscRow?.classList.add('flex');
                    productDiscEl.textContent = '− ' + fmtRub(data.product_discount);
                } else {
                    productDiscRow?.classList.add('hidden');
                    productDiscRow?.classList.remove('flex');
                }
                if (Number(data.promocode_discount) > 0 && data.promocode?.valid) {
                    promoDiscRow?.classList.remove('hidden');
                    promoDiscRow?.classList.add('flex');
                    promoDiscEl.textContent = '− ' + fmtRub(data.promocode_discount);
                } else {
                    promoDiscRow?.classList.add('hidden');
                    promoDiscRow?.classList.remove('flex');
                }
                if (Number(data.loyalty_discount) > 0) {
                    loyaltyDiscRow?.classList.remove('hidden');
                    loyaltyDiscRow?.classList.add('flex');
                    loyaltyDiscEl.textContent = '− ' + fmtRub(data.loyalty_discount);
                } else {
                    loyaltyDiscRow?.classList.add('hidden');
                    loyaltyDiscRow?.classList.remove('flex');
                }
                totEl.textContent = fmtRub(data.total);
                if (data.promocode?.message && promoInput?.value?.trim()) {
                    hint.textContent = data.promocode.message;
                    hint.classList.remove('hidden');
                    hint.classList.toggle('text-rose-600', !data.promocode.valid);
                    hint.classList.toggle('text-emerald-700', data.promocode.valid);
                }
            }
            const schedulePreview = () => {
                clearTimeout(t);
                t = setTimeout(preview, 320);
            };
            promoInput?.addEventListener('input', schedulePreview);
            loyaltyCheckbox?.addEventListener('change', schedulePreview);
            preview();
        })();
    </script>
    @endpush
@endsection
