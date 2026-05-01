@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">Админ-панель ДЯБ</h1>
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
    <script>
        new Chart(document.getElementById('sales-chart'), {
            type: 'line',
            data: {
                labels: @json($sales->pluck('day')),
                datasets: [{ label: 'Сумма', data: @json($sales->pluck('total')), borderColor: '#1f2937' }]
            }
        });
        new Chart(document.getElementById('promo-chart'), {
            type: 'bar',
            data: {
                labels: @json($promocodeUsage->pluck('code')),
                datasets: [{ label: 'Использований', data: @json($promocodeUsage->pluck('total')), backgroundColor: '#a8a29e' }]
            }
        });
    </script>
@endsection
