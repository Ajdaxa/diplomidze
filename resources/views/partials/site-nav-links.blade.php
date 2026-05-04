{{-- Общие ссылки шапки: $variant = 'desktop' | 'mobile' --}}
@php
    $cartCount = $cartCount ?? collect(session('cart', []))->sum();
    $isMobile = ($variant ?? 'desktop') === 'mobile';
    $linkClass = $isMobile
        ? 'flex min-h-11 items-center rounded-lg px-3 text-sm font-medium uppercase tracking-wider text-neutral-800 hover:bg-neutral-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900'
        : 'inline-flex min-h-9 items-center rounded-md px-2 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:text-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900';
@endphp
<a href="{{ route('home') }}" class="{{ $linkClass }}">Главная</a>
<a href="{{ route('catalog') }}" class="{{ $linkClass }}">Каталог</a>
<a href="{{ route('favorites.index') }}" class="{{ $linkClass }}">Избранное</a>
<a href="{{ route('cart.index') }}" class="{{ $linkClass }} inline-flex items-center gap-1.5">
    Корзина
    @if($cartCount > 0)
        <span class="rounded-full bg-black px-2 py-0.5 text-[10px] text-white">{{ $cartCount }}</span>
    @endif
</a>
@auth
    @if(auth()->user()->hasRole('client'))
        <a href="{{ route('profile.show') }}" class="{{ $linkClass }}">Профиль</a>
    @endif
    @if(auth()->user()->hasRole('courier'))
        <a href="{{ route('courier.orders.index') }}" class="{{ $linkClass }}">Доставка</a>
    @endif
    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
        <a href="{{ route('admin.hub') }}" class="{{ $linkClass }} font-semibold text-neutral-900">Админ-панель</a>
    @endif
@else
    <a href="{{ route('otp.form') }}" class="{{ $linkClass }}">Войти</a>
@endauth
