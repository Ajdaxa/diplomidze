@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-stone-200 bg-white p-6">
        <h1 class="mb-2 text-2xl font-semibold">Регистрация</h1>
        <p class="mb-6 text-sm text-stone-500">Создайте аккаунт Дəб за минуту.</p>
        <form method="POST" action="{{ route('otp.register') }}" class="space-y-3">
            @csrf
            <input name="name" placeholder="Имя" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <input type="email" name="email" placeholder="Email" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <input name="phone" placeholder="Телефон (необязательно)" class="w-full rounded-lg border border-stone-300 px-3 py-2">
            <input type="password" name="password" placeholder="Пароль" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <input type="password" name="password_confirmation" placeholder="Подтвердите пароль" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-white">Создать аккаунт</button>
        </form>
        <a href="{{ route('otp.form', session()->has('url.intended') ? ['redirect' => session('url.intended')] : []) }}" class="mt-4 inline-block text-sm text-stone-500 hover:text-black">Назад</a>
    </div>
@endsection
