@extends('layouts.admin')

@section('title', 'Аналитика')
@section('heading', 'Панель KPI')

@section('content')
    <div class="mb-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Заказы сегодня</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $todayOrders }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Выручка сегодня</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums">{{ number_format($todayRevenue, 0, '.', ' ') }} ₽</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">За 7 дней</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums">{{ number_format($weekRevenue, 0, '.', ' ') }} ₽</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Средний чек</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums">{{ number_format($avgOrderValue, 0, '.', ' ') }} ₽</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-amber-800">Ожидают оплаты</p>
            <p class="mt-2 text-2xl font-semibold text-amber-900">{{ $pendingOrders }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-neutral-500">Курьеры</p>
            <p class="mt-2 text-2xl font-semibold">{{ $activeCouriersCount }}</p>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-4 lg:col-span-2">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider">Продажи за неделю</h2>
            <canvas id="sales-chart" height="100"></canvas>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider">По статусам</h2>
            <ul class="space-y-2 text-sm">
                @foreach($ordersByStatus as $row)
                    <li class="flex justify-between gap-2 border-b border-neutral-50 py-2 last:border-0">
                        <span class="text-neutral-600">{{ $row['label'] }}</span>
                        <span class="font-semibold tabular-nums">{{ $row['count'] }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-4 text-xs text-neutral-500">Оплачено, ждут курьера: <strong>{{ $paidAwaitingCourier }}</strong></p>
            <p class="text-xs text-neutral-500">Отзывов на модерации: <a href="{{ route('admin.reviews.index') }}" class="font-semibold underline">{{ $pendingReviews }}</a></p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-wider">Мало на складе</h2>
                <span class="text-xs text-neutral-500">Нет в наличии: {{ $outOfStockCount }}</span>
            </div>
            <ul class="divide-y divide-neutral-100 text-sm">
                @forelse($lowStockProducts as $p)
                    <li class="flex justify-between gap-3 py-2">
                        <a href="{{ route('admin.products.edit', $p) }}" class="truncate hover:underline">{{ $p->name }}</a>
                        <span class="shrink-0 font-mono text-xs text-amber-700">{{ $p->stock }} шт.</span>
                    </li>
                @empty
                    <li class="py-4 text-neutral-500">Все позиции с запасом выше 3 шт.</li>
                @endforelse
            </ul>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider">Последние заказы</h2>
            <ul class="space-y-3 text-sm">
                @foreach($recentOrders as $order)
                    <li class="flex items-start justify-between gap-3 border-b border-neutral-50 pb-3 last:border-0">
                        <div>
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-medium hover:underline">#{{ $order->id }}</a>
                            <p class="text-xs text-neutral-500">{{ $order->user?->name ?? '—' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">{{ number_format($order->total_price, 0, '.', ' ') }} ₽</p>
                            <p class="text-[10px] uppercase text-neutral-500">{{ \App\Support\OrderStatus::label($order->status) }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-4">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider">Промокоды — использования</h2>
        <canvas id="promo-chart" height="80"></canvas>
    </div>

    <div id="dashboard-data"
         data-sales-labels='@json($sales->pluck("day")->values())'
         data-sales-totals='@json($sales->pluck("total")->values())'
         data-promo-labels='@json($promocodeUsage->pluck("code")->values())'
         data-promo-totals='@json($promocodeUsage->pluck("total")->values())'></div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', () => {
            if (!window.Chart) return;
            const payload = document.getElementById('dashboard-data');
            if (!payload) return;
            const salesLabels = JSON.parse(payload.dataset.salesLabels || '[]');
            const salesTotals = JSON.parse(payload.dataset.salesTotals || '[]');
            const promoLabels = JSON.parse(payload.dataset.promoLabels || '[]');
            const promoTotals = JSON.parse(payload.dataset.promoTotals || '[]');
            new Chart(document.getElementById('sales-chart'), {
                type: 'line',
                data: {
                    labels: salesLabels,
                    datasets: [{ label: '₽', data: salesTotals, borderColor: '#171717', tension: 0.3, fill: false }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
            if (promoLabels.length) {
                new Chart(document.getElementById('promo-chart'), {
                    type: 'bar',
                    data: {
                        labels: promoLabels,
                        datasets: [{ label: 'Исп.', data: promoTotals, backgroundColor: '#a3a3a3' }]
                    },
                    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
                });
            }
        });
    </script>
@endpush
