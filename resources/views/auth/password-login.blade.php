@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-stone-200 bg-white p-6">
        <h1 class="mb-2 text-2xl font-semibold">Вход по паролю</h1>
        <p class="mb-6 text-sm text-stone-500">Авторизуйтесь по email или номеру телефона.</p>
        <form method="POST" action="{{ route('otp.password') }}" class="space-y-3">
            @csrf
            <input name="identity" placeholder="Email или телефон" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <input type="password" name="password" placeholder="Пароль" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <button class="w-full rounded-lg bg-stone-900 px-4 py-2 text-white">Войти</button>
        </form>
        <a href="{{ route('otp.form', session()->has('url.intended') ? ['redirect' => session('url.intended')] : []) }}" class="mt-4 inline-block text-sm text-stone-500 hover:text-black">Назад</a>
    </div>
@endsection
