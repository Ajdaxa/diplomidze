@extends('layouts.admin')

@section('title', 'Клиенты')
@section('heading', 'Клиенты')

@section('content')
    <p class="mb-6 max-w-2xl text-sm text-neutral-600">
        Только покупатели витрины: контакты для поддержки и заказов. Пароли и служебные аккаунты здесь не отображаются.
    </p>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center">
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="Имя, email или телефон"
            class="w-full max-w-md rounded-lg border border-neutral-300 px-3 py-2 text-sm"
        >
        <button type="submit" class="rounded-lg bg-black px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white">Найти</button>
        @if($search !== '')
            <a href="{{ route('admin.users.index') }}" class="text-xs text-neutral-500 underline hover:text-black">Сбросить</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="border-b border-neutral-200 bg-neutral-50 text-[10px] font-semibold uppercase tracking-wider text-neutral-500">
                    <tr>
                        <th class="px-4 py-3">Клиент</th>
                        <th class="px-4 py-3">Контакты</th>
                        <th class="px-4 py-3">Заказы</th>
                        <th class="px-4 py-3">Баллы</th>
                        <th class="px-4 py-3">Регистрация</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($users as $user)
                        <tr class="transition hover:bg-neutral-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-neutral-900 underline decoration-neutral-300 underline-offset-2 hover:decoration-black">
                                    {{ $user->name }}
                                </a>
                                <p class="text-xs text-neutral-400">#{{ $user->id }}</p>
                            </td>
                            <td class="px-4 py-3 text-neutral-600">
                                <p>{{ $user->email }}</p>
                                <p class="text-xs">{{ $user->phone ?: '—' }}</p>
                            </td>
                            <td class="px-4 py-3 tabular-nums">
                                {{ $user->client_orders_count }}
                                @if($user->paid_orders_count > 0)
                                    <span class="text-xs text-neutral-400">({{ $user->paid_orders_count }} оплач.)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 tabular-nums">{{ number_format($user->loyalty_points, 0, '.', ' ') }}</td>
                            <td class="px-4 py-3 text-neutral-500">{{ $user->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-neutral-500">Клиенты не найдены.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
