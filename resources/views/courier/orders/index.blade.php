@extends('layouts.app')

@section('content')
    <h1 class="mb-6 text-3xl font-semibold">Кабинет курьера</h1>
    <div class="space-y-4">
        @foreach($orders as $order)
            <div class="rounded-xl border border-stone-200 bg-white p-4">
                <p class="font-medium">Заказ #{{ $order->id }}</p>
                <p class="text-sm text-stone-500">Статус: {{ $order->status }}</p>
                <div class="mt-3 flex gap-2">
                    <form method="POST" action="{{ route('courier.orders.arrived', $order) }}">
                        @csrf
                        <button class="rounded bg-stone-900 px-3 py-2 text-sm text-white">Я на месте</button>
                    </form>
                    <form method="POST" action="{{ route('courier.orders.delivered', $order) }}">
                        @csrf
                        <button class="rounded border border-stone-300 px-3 py-2 text-sm">Доставлено</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
