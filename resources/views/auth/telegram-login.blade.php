@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-stone-200 bg-white p-6">
        <h1 class="mb-2 text-2xl font-semibold">Telegram вход</h1>
        <p class="mb-6 text-sm text-stone-500">Войдите по коду из Telegram или создайте новый аккаунт через Telegram.</p>

        <form method="POST" action="{{ route('otp.send') }}" class="space-y-3">
            @csrf
            <input name="identity" placeholder="Email или телефон" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <button class="w-full rounded-lg bg-sky-600 px-4 py-2 text-white">Отправить код в Telegram</button>
            <p class="text-xs text-stone-500">Если Telegram не привязан, получите ссылку для команды <code>/start token</code>.</p>
        </form>

        <form method="POST" action="{{ route('otp.verify') }}" class="mt-4 space-y-3">
            @csrf
            <input name="code" maxlength="4" placeholder="4-значный код" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <button class="w-full rounded-lg border border-stone-300 px-4 py-2">Подтвердить вход</button>
        </form>

        <form method="POST" action="{{ route('otp.telegram_autoreg') }}" class="mt-4">
            @csrf
            <button class="w-full rounded-lg bg-[#229ED9] px-4 py-2 text-white">Авторегистрация через Telegram</button>
        </form>

        <a href="{{ route('otp.form', session()->has('url.intended') ? ['redirect' => session('url.intended')] : []) }}" class="mt-4 inline-block text-sm text-stone-500 hover:text-black">Назад</a>
    </div>
@endsection
