@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-neutral-200 bg-white p-6">
        <h1 class="mb-2 text-2xl font-semibold">Регистрация</h1>
        <p class="mb-6 text-sm text-neutral-500">Создайте аккаунт Дəб за минуту.</p>
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                <p class="font-medium">Проверьте поля формы</p>
                <p class="mt-1 text-xs text-rose-800">{{ $errors->first() }}</p>
            </div>
        @endif
        <form method="POST" action="{{ route('otp.register') }}" class="space-y-4" novalidate>
            @csrf
            <x-floating-input name="name" label="Имя" autocomplete="name" :required="true" />
            <x-floating-input name="email" type="email" label="Email" autocomplete="email" :required="true" />
            <x-floating-input name="phone" label="Телефон" autocomplete="tel" inputmode="tel" :required="true" />
            <div>
                <x-floating-input name="password" type="password" label="Пароль" autocomplete="new-password" :required="true" />
                <p class="mt-1.5 text-[11px] text-neutral-500">Минимум 6 символов.</p>
            </div>
            <x-floating-input name="password_confirmation" type="password" label="Повторите пароль" autocomplete="new-password" :required="true" />
            <button class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Создать аккаунт</button>
        </form>
        <a href="{{ route('otp.form', session()->has('url.intended') ? ['redirect' => session('url.intended')] : []) }}" class="mt-4 inline-block text-sm text-neutral-500 hover:text-black">Назад</a>
    </div>
@endsection
