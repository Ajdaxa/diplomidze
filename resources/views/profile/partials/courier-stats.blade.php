@if($courierStats)
    <section class="mb-8 overflow-hidden rounded-2xl border border-neutral-200 bg-white sm:mb-10">
        <div class="border-b border-neutral-100 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 px-5 py-6 text-white sm:px-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-white/70">Кабинет курьера</p>
                    <h2 class="mt-2 text-2xl font-light">Ваша статистика</h2>
                    <p class="mt-2 max-w-lg text-sm text-white/85">
                        Комиссия {{ number_format($courierStats['commission_percent'], 0, '.', ' ') }}% от суммы каждого доставленного заказа.
                    </p>
                </div>
                <a href="{{ route('courier.orders.index') }}" class="inline-flex min-h-10 items-center rounded-xl border border-white/30 bg-white/10 px-4 text-xs font-semibold uppercase tracking-wider text-white backdrop-blur-sm transition hover:bg-white/20">
                    К заказам
                </a>
            </div>
        </div>

        <div class="grid gap-px bg-neutral-200 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['label' => 'Сегодня', 'data' => $courierStats['today']],
                ['label' => '7 дней', 'data' => $courierStats['week']],
                ['label' => 'Месяц', 'data' => $courierStats['month']],
                ['label' => 'Всего', 'data' => $courierStats['all_time']],
            ] as $block)
                <div class="bg-white p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-neutral-500">{{ $block['label'] }}</p>
                    <p class="mt-3 text-2xl font-semibold tabular-nums text-neutral-900">{{ number_format($block['data']['earnings'], 0, '.', ' ') }} ₽</p>
                    <p class="mt-1 text-xs text-neutral-500">заработок</p>
                    <div class="mt-4 flex flex-wrap gap-3 text-xs text-neutral-600">
                        <span class="rounded-full bg-neutral-100 px-2.5 py-1">{{ $block['data']['count'] }} заказов</span>
                        <span class="rounded-full bg-neutral-100 px-2.5 py-1">на {{ number_format($block['data']['orders_total'], 0, '.', ' ') }} ₽</span>
                    </div>
                </div>
            @endforeach
        </div>

        @if($courierStats['recent_deliveries']->isNotEmpty())
            <div class="border-t border-neutral-100 p-5 sm:p-6">
                <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500">Последние доставки</h3>
                <ul class="mt-4 divide-y divide-neutral-100">
                    @foreach($courierStats['recent_deliveries'] as $delivery)
                        @php
                            $earning = round((float) $delivery->total_price * $courierStats['commission_percent'] / 100, 2);
                        @endphp
                        <li class="flex flex-wrap items-center justify-between gap-3 py-3 text-sm">
                            <div>
                                <p class="font-medium">Заказ #{{ $delivery->id }}</p>
                                <p class="text-xs text-neutral-500">{{ $delivery->updated_at->format('d.m.Y H:i') }} · {{ $delivery->user?->name ?? 'Клиент' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold tabular-nums text-emerald-700">+ {{ number_format($earning, 0, '.', ' ') }} ₽</p>
                                <p class="text-xs text-neutral-500">{{ number_format($delivery->total_price, 0, '.', ' ') }} ₽</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
@endif
