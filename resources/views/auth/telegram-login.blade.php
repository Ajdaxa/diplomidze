@extends('layouts.app')

@section('title', 'Вход через Telegram')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
        <h1 class="mb-2 text-2xl font-semibold">Telegram вход</h1>
        <p class="mb-6 text-sm text-neutral-500">Двухшаговый вход: привязка в боте, затем автоматический вход на сайте по ссылке из Telegram.</p>

        @if(session('status'))
            <div class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                {{ $errors->first() }}
            </div>
        @endif

        <ol class="mb-6 space-y-2 rounded-xl border border-neutral-100 bg-neutral-50 p-4 text-sm text-neutral-700">
            <li><span class="font-semibold text-neutral-900">1.</span> Нажмите «Создать аккаунт» или войдите по email, если уже регистрировались.</li>
            <li><span class="font-semibold text-neutral-900">2.</span> Откройте бота в Telegram и нажмите <strong>Start</strong> (команда <code>/start</code> с токеном).</li>
            <li><span class="font-semibold text-neutral-900">3.</span> В боте придёт ссылка «войти на сайт» — откройте её в браузере.</li>
        </ol>

        @if(session('telegram_pending_email'))
            <p class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                Ваш email для входа по коду: <span class="font-mono font-semibold">{{ session('telegram_pending_email') }}</span>
            </p>
        @endif

        <form method="POST" action="{{ route('otp.telegram_autoreg') }}" class="mb-6">
            @csrf
            <button type="submit" class="w-full rounded-xl bg-[#229ED9] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#1a8bc4]">Создать аккаунт и привязать Telegram</button>
        </form>

        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-neutral-500">Уже привязали Telegram?</p>
        <form method="POST" action="{{ route('otp.send') }}" class="space-y-3">
            @csrf
            <x-floating-input
                name="identity"
                label="Email или телефон"
                :value="old('identity', session('telegram_pending_email'))"
                autocomplete="username"
                :required="true"
            />
            <button type="submit" class="w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-700">Отправить код в Telegram</button>
        </form>

        <form method="POST" action="{{ route('otp.verify') }}" class="mt-4 space-y-3">
            @csrf
            <x-floating-input name="code" label="Код из Telegram (4 цифры)" inputmode="numeric" autocomplete="one-time-code" :required="true" />
            <button type="submit" class="w-full rounded-xl border border-neutral-300 px-4 py-3 text-sm font-semibold hover:border-neutral-900">Подтвердить код</button>
        </form>

        <p class="mt-4 text-xs text-neutral-500">
            Для работы бота на сервере нужны <code>TELEGRAM_BOT_TOKEN</code>, <code>TELEGRAM_BOT_USERNAME</code> и HTTPS webhook:
            <code class="block mt-1 break-all">php artisan telegram:set-webhook</code>
        </p>

        <a href="{{ route('otp.form', session()->has('url.intended') ? ['redirect' => session('url.intended')] : []) }}" class="mt-6 inline-block text-sm text-neutral-500 hover:text-black">Назад</a>
    </div>
@endsection
