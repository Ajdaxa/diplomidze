@extends('layouts.app')

@section('title', 'Политика конфиденциальности')

@section('content')
    <article class="mx-auto max-w-3xl">
        <x-page-heading title="Политика конфиденциальности" lede="Как мы обрабатываем персональные данные" />

        <div class="space-y-6 text-sm leading-relaxed text-neutral-700">
            <section>
                <h2 class="text-base font-semibold uppercase tracking-wider text-neutral-900">1. Какие данные собираем</h2>
                <p>Имя, email, телефон, адрес доставки, данные заказов, идентификатор Telegram при привязке аккаунта.</p>
            </section>
            <section>
                <h2 class="text-base font-semibold uppercase tracking-wider text-neutral-900">2. Цели обработки</h2>
                <p>Оформление и доставка заказов, поддержка клиентов, уведомления о статусе заказа, программа лояльности.</p>
            </section>
            <section>
                <h2 class="text-base font-semibold uppercase tracking-wider text-neutral-900">3. Передача третьим лицам</h2>
                <p>Платёжному сервису YooKassa — для приёма оплаты; службам доставки — для выполнения заказа; Telegram — при вашем согласии на уведомления.</p>
            </section>
            <section>
                <h2 class="text-base font-semibold uppercase tracking-wider text-neutral-900">4. Ваши права</h2>
                <p>Запросить уточнение или удаление данных можно по адресу <a href="mailto:{{ config('site.support_email') }}" class="underline">{{ config('site.support_email') }}</a>.</p>
            </section>
        </div>

        <p class="mt-10">
            <a href="{{ route('home') }}" class="text-xs font-semibold uppercase tracking-wider text-neutral-600 underline hover:text-black">На главную</a>
        </p>
    </article>
@endsection
