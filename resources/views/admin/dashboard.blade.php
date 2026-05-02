@extends('layouts.admin')

@section('title', 'Аналитика')
@section('heading', 'Аналитика')

@section('content')
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Активные курьеры</p>
            <p class="mt-2 text-3xl font-semibold">{{ $activeCouriersCount }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider">Продажи за неделю</h2>
            <canvas id="sales-chart" height="120"></canvas>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider">Промокоды</h2>
            <canvas id="promo-chart" height="120"></canvas>
        </div>
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
                    datasets: [{ label: 'Сумма', data: salesTotals, borderColor: '#171717' }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });
            new Chart(document.getElementById('promo-chart'), {
                type: 'bar',
                data: {
                    labels: promoLabels,
                    datasets: [{ label: 'Использований', data: promoTotals, backgroundColor: '#a3a3a3' }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
@endpush
