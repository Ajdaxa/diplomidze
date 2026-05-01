@extends('layouts.app')

@section('content')
    <div class="mx-auto mt-16 max-w-xl text-center">
        <p class="text-sm uppercase tracking-widest text-stone-500">ДЯБ</p>
        <h1 class="mt-4 text-5xl font-semibold">404</h1>
        <p class="mt-3 text-stone-600">Эта страница не найдена. Вернем вас к премиальной витрине.</p>
        <a href="{{ route('home') }}" class="mt-6 inline-block rounded-lg bg-stone-900 px-5 py-3 text-white">На главную</a>
    </div>
@endsection
