@php
    $supportEmail = config('site.support_email');
@endphp
<footer class="mt-auto border-t border-neutral-200 bg-neutral-50">
    <div class="container-app py-10 sm:py-12">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 lg:gap-10">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500">Дəб</p>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-neutral-600">Одежда и аксессуары с акцентом на качество и лаконичный силуэт.</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Навигация</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-neutral-700 hover:text-black hover:underline">Главная</a></li>
                    <li><a href="{{ route('catalog') }}" class="text-neutral-700 hover:text-black hover:underline">Каталог</a></li>
                    <li><a href="{{ route('home') }}#contacts" class="text-neutral-700 hover:text-black hover:underline">Контакты</a></li>
                    <li><a href="{{ route('cart.index') }}" class="text-neutral-700 hover:text-black hover:underline">Корзина</a></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Документы</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('pages.offer') }}" class="text-neutral-700 hover:text-black hover:underline">Публичная оферта</a></li>
                    <li><a href="{{ route('pages.privacy') }}" class="text-neutral-700 hover:text-black hover:underline">Конфиденциальность</a></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Покупателям</p>
                <ul class="mt-3 space-y-2 text-sm">
                    @auth
                        <li><a href="{{ route('profile.show') }}" class="text-neutral-700 hover:text-black hover:underline">Профиль</a></li>
                        <li><a href="{{ route('favorites.index') }}" class="text-neutral-700 hover:text-black hover:underline">Избранное</a></li>
                    @else
                        <li><a href="{{ route('otp.form') }}" class="text-neutral-700 hover:text-black hover:underline">Вход</a></li>
                    @endauth
                </ul>
            </div>
            <div id="contacts-footer">
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Связь</p>
                <ul class="mt-3 space-y-2 text-sm text-neutral-700">
                    <li><a href="mailto:{{ $supportEmail }}" class="hover:text-black hover:underline">{{ $supportEmail }}</a></li>
                    <li><span class="text-neutral-600">Пн–Вс, 10:00–20:00</span></li>
                    <li><span class="text-neutral-600">Доставка по городу и РФ</span></li>
                </ul>
            </div>
        </div>
        <div class="mt-10 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 border-t border-neutral-200 pt-8 text-[10px] font-semibold uppercase tracking-[0.2em] text-neutral-500 sm:gap-x-10">
            <span class="inline-flex items-center gap-1.5">Безопасная оплата</span>
            <span class="inline-flex items-center gap-1.5">Курьерская доставка</span>
            <span class="inline-flex items-center gap-1.5">Поддержка 7/7</span>
        </div>
        <p class="mt-6 text-center text-xs text-neutral-500">© {{ date('Y') }} Дəб. Все права защищены.</p>
    </div>
</footer>
