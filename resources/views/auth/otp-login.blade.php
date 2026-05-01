@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-stone-200 bg-white p-6">
        <h1 class="mb-4 text-2xl font-semibold">Вход в ДЯБ</h1>

        <form method="POST" action="{{ route('otp.send') }}" class="mb-6 space-y-3">
            @csrf
            <label class="block text-sm">Email или телефон</label>
            <input name="identity" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <button class="rounded-lg bg-stone-900 px-4 py-2 text-white">Отправить код в Telegram</button>
        </form>
        <p class="mb-6 text-xs text-stone-500">
            Если Telegram не привязан, система покажет ссылку для команды <code>/start token</code>.
        </p>

        <div class="mb-6 border-t border-stone-200 pt-6">
            <h2 class="mb-3 text-lg font-medium">Вход по паролю</h2>
            <form method="POST" action="{{ route('otp.password') }}" class="space-y-3">
                @csrf
                <input name="identity" placeholder="Email или телефон" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
                <input type="password" name="password" placeholder="Пароль" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
                <button class="rounded-lg bg-stone-900 px-4 py-2 text-white">Войти по паролю</button>
            </form>
        </div>

        <form method="POST" action="{{ route('otp.verify') }}" class="space-y-3">
            @csrf
            <label class="block text-sm">4-значный код</label>
            <input name="code" maxlength="4" class="w-full rounded-lg border border-stone-300 px-3 py-2" required>
            <button class="rounded-lg border border-stone-300 px-4 py-2">Подтвердить вход</button>
        </form>
    </div>
@endsection
