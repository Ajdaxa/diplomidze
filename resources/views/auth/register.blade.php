@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-stone-200 bg-white p-6">
        <h1 class="mb-2 text-2xl font-semibold">Регистрация</h1>
        <p class="mb-6 text-sm text-stone-500">Создайте аккаунт Дəб за минуту.</p>
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                <p class="font-medium">Проверьте поля формы</p>
                <p class="mt-1 text-xs text-rose-800">{{ $errors->first() }}</p>
            </div>
        @endif
        <form method="POST" action="{{ route('otp.register') }}" class="space-y-3" novalidate>
            @csrf
            <div>
                <input name="name" placeholder="Имя" value="{{ old('name') }}"
                       class="w-full rounded-lg border px-3 py-2 outline-none transition focus:border-black @error('name') border-rose-300 bg-rose-50 @else border-stone-300 @enderror">
                @error('name')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                       class="w-full rounded-lg border px-3 py-2 outline-none transition focus:border-black @error('email') border-rose-300 bg-rose-50 @else border-stone-300 @enderror">
                @error('email')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <input name="phone" placeholder="Телефон (необязательно)" value="{{ old('phone') }}"
                       class="w-full rounded-lg border px-3 py-2 outline-none transition focus:border-black @error('phone') border-rose-300 bg-rose-50 @else border-stone-300 @enderror">
                @error('phone')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <input type="password" name="password" placeholder="Пароль"
                       class="w-full rounded-lg border px-3 py-2 outline-none transition focus:border-black @error('password') border-rose-300 bg-rose-50 @else border-stone-300 @enderror">
                @error('password')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                <p class="mt-1 text-[11px] text-stone-500">Минимум 6 символов.</p>
            </div>
            <div>
                <input type="password" name="password_confirmation" placeholder="Подтвердите пароль"
                       class="w-full rounded-lg border px-3 py-2 outline-none transition focus:border-black border-stone-300">
            </div>
            <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-white">Создать аккаунт</button>
        </form>
        <a href="{{ route('otp.form', session()->has('url.intended') ? ['redirect' => session('url.intended')] : []) }}" class="mt-4 inline-block text-sm text-stone-500 hover:text-black">Назад</a>
    </div>
@endsection
