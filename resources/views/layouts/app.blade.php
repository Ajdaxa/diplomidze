<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ДЯБ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-stone-50 text-stone-800">
<div class="min-h-screen">
    <header class="border-b border-stone-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="text-xl font-semibold tracking-wider">ДЯБ</a>
            <nav class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('checkout.create') }}" class="hover:text-black">Оформление</a>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button>Выход</button></form>
                @else
                    <a href="{{ route('otp.form') }}" class="hover:text-black">Войти</a>
                @endauth
            </nav>
        </div>
    </header>
    <main class="mx-auto max-w-6xl px-6 py-8">
        @if (session('status'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
