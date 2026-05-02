@extends('layouts.app')

@section('content')
    <div class="mx-auto mt-16 max-w-xl text-center">
        <p class="text-sm uppercase tracking-widest text-stone-500">Дəб</p>
        <h1 class="mt-4 text-5xl font-semibold">500</h1>
        <p class="mt-3 text-stone-600">Ай дана, сервер устал. Сейчас поправим и снова вернем люкс-режим.</p>
        <a href="{{ route('home') }}" class="mt-6 inline-block rounded-lg border border-stone-300 px-5 py-3">Обновить</a>
    </div>
@endsection
