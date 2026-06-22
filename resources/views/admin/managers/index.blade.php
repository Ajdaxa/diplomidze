@extends('layouts.admin')

@section('title', 'Менеджеры')
@section('heading', 'Менеджеры')

@section('content')
    <p class="mb-6 max-w-2xl text-sm text-neutral-600">
        Назначайте сотрудников с ролью менеджера: доступ к заказам, клиентам, отзывам и аналитике без прав на редактирование каталога.
    </p>

    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 sm:p-6">
        <h2 class="mb-4 text-xs font-semibold uppercase tracking-wider text-neutral-500">Назначить менеджера</h2>
        <form method="POST" action="{{ route('admin.managers.store') }}" class="grid grid-cols-1 gap-3 md:grid-cols-2">
            @csrf
            <input name="name" placeholder="Имя" value="{{ old('name') }}" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm" required>
            <input name="phone" placeholder="Телефон" value="{{ old('phone') }}" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm" required>
            <input name="email" type="email" placeholder="Email для входа" value="{{ old('email') }}" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm md:col-span-2" required>
            <input name="password" type="password" placeholder="Пароль для входа" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm md:col-span-2" required>
            <button class="md:col-span-2 inline-flex min-h-10 items-center justify-center rounded-lg bg-black px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white hover:bg-neutral-800">
                Создать менеджера
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @forelse($managers as $manager)
            <div class="rounded-xl border border-neutral-200 bg-white p-4 sm:p-5">
                <div class="mb-3 flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-medium text-neutral-900">{{ $manager->name }}</h3>
                        <p class="text-xs text-neutral-500">ID #{{ $manager->id }} · с {{ $manager->created_at->format('d.m.Y') }}</p>
                    </div>
                    <span class="rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-sky-700">Менеджер</span>
                </div>
                <form method="POST" action="{{ route('admin.managers.update', $manager) }}" class="space-y-2">
                    @csrf @method('PATCH')
                    <input name="name" value="{{ old('name', $manager->name) }}" class="w-full rounded-lg border border-neutral-300 px-2 py-1.5 text-sm" required>
                    <input name="phone" value="{{ old('phone', $manager->phone) }}" class="w-full rounded-lg border border-neutral-300 px-2 py-1.5 text-sm" required>
                    <input name="email" type="email" value="{{ old('email', $manager->email) }}" class="w-full rounded-lg border border-neutral-300 px-2 py-1.5 text-sm" required>
                    <input name="password" type="password" placeholder="Новый пароль (опционально)" class="w-full rounded-lg border border-neutral-300 px-2 py-1.5 text-sm">
                    <button class="rounded-lg border border-neutral-300 px-3 py-1.5 text-sm font-medium hover:border-black">Сохранить</button>
                </form>
                @if($manager->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.managers.destroy', $manager) }}" class="mt-3" onsubmit="return confirm('Снять менеджера с должности и удалить аккаунт?');">
                        @csrf @method('DELETE')
                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-sm text-rose-700 hover:bg-rose-50">Удалить</button>
                    </form>
                @else
                    <p class="mt-3 text-xs text-neutral-500">Это ваш аккаунт — удаление недоступно.</p>
                @endif
            </div>
        @empty
            <p class="col-span-full rounded-xl border border-dashed border-neutral-300 bg-white p-8 text-center text-sm text-neutral-500">
                Менеджеров пока нет. Создайте первого через форму выше.
            </p>
        @endforelse
    </div>

    @if($managers->hasPages())
        <div class="mt-6">{{ $managers->links() }}</div>
    @endif
@endsection
