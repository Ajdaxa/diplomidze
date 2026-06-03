@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-stone-200 bg-white p-6">
        <h1 class="mb-2 text-2xl font-semibold">Добро пожаловать в Дəб</h1>
        <p class="mb-6 text-sm text-stone-500">Войдите по паролю или создайте новый аккаунт.</p>
        <div class="space-y-3">
            <a href="{{ route('otp.password.form', request()->filled('redirect') ? ['redirect' => request('redirect')] : []) }}" class="block w-full rounded-lg bg-stone-900 px-4 py-3 text-center text-sm text-white">Войти по паролю</a>
            <a href="{{ route('otp.register.form', request()->filled('redirect') ? ['redirect' => request('redirect')] : []) }}" class="block w-full rounded-lg border border-stone-300 px-4 py-3 text-center text-sm">Зарегистрироваться</a>
        </div>
    </div>
@endsection
