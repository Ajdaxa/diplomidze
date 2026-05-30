@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-neutral-200 bg-white p-6">
        <h1 class="mb-2 text-2xl font-semibold">Вход по паролю</h1>
        <p class="mb-6 text-sm text-neutral-500">Авторизуйтесь по email или номеру телефона.</p>
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                <p class="font-medium">Не удалось войти</p>
                <p class="mt-1 text-xs text-rose-800">{{ $errors->first() }}</p>
            </div>
        @endif
        <form method="POST" action="{{ route('otp.password') }}" class="space-y-4" novalidate>
            @csrf
            <x-floating-input name="identity" label="Email или телефон" :value="old('identity')" autocomplete="username" :required="true" />
            <x-floating-input name="password" type="password" label="Пароль" autocomplete="current-password" :required="true" />
            <button class="w-full rounded-xl bg-neutral-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-neutral-800">Войти</button>
        </form>
        <a href="{{ route('otp.form', session()->has('url.intended') ? ['redirect' => session('url.intended')] : []) }}" class="mt-4 inline-block text-sm text-neutral-500 hover:text-black">Назад</a>
    </div>
@endsection
