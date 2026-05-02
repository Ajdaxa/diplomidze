@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">Админ-панель ДЯБ</h1>
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-stone-200 bg-white p-4">
            <p class="text-sm text-stone-500">Активные курьеры</p>
            <p class="text-3xl font-semibold">{{ $activeCouriersCount }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white p-4">
            <h2 class="mb-3 text-lg font-medium">Продажи за неделю</h2>
            <canvas id="sales-chart"></canvas>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-4">
            <h2 class="mb-3 text-lg font-medium">Использование промокодов</h2>
            <canvas id="promo-chart"></canvas>
        </div>
    </div>
    <div id="dashboard-data"
         data-sales-labels='@json($sales->pluck("day")->values())'
         data-sales-totals='@json($sales->pluck("total")->values())'
         data-promo-labels='@json($promocodeUsage->pluck("code")->values())'
         data-promo-totals='@json($promocodeUsage->pluck("total")->values())'></div>
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
                    datasets: [{ label: 'Сумма', data: salesTotals, borderColor: '#1f2937' }]
                }
            });
            new Chart(document.getElementById('promo-chart'), {
                type: 'bar',
                data: {
                    labels: promoLabels,
                    datasets: [{ label: 'Использований', data: promoTotals, backgroundColor: '#a8a29e' }]
                }
            });
        });
    </script>
@endsection
