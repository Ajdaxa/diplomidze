@extends('layouts.app')

@section('title', 'Публичная оферта')

@section('content')
    <article class="mx-auto max-w-3xl prose prose-neutral prose-sm sm:prose-base">
        <x-page-heading title="Публичная оферта" lede="Условия покупки в интернет-магазине Дəб" />

        <div class="space-y-6 text-sm leading-relaxed text-neutral-700 [&_h2]:text-base [&_h2]:font-semibold [&_h2]:uppercase [&_h2]:tracking-wider [&_h2]:text-neutral-900">
            <section>
                <h2>1. Общие положения</h2>
                <p>Настоящая оферта регулирует отношения между покупателем и интернет-магазином «Дəб» при оформлении и оплате заказов на сайте.</p>
            </section>
            <section>
                <h2>2. Оформление заказа</h2>
                <p>Покупатель указывает достоверный адрес доставки и контактные данные. Заказ считается принятым после успешной оплаты через YooMoney.</p>
            </section>
            <section>
                <h2>3. Доставка и возврат</h2>
                <p>Сроки и стоимость доставки согласуются при обработке заказа. Возврат товара надлежащего качества — в соответствии с законодательством РФ.</p>
            </section>
            <section>
                <h2>4. Контакты</h2>
                <p>По вопросам заказов: <a href="mailto:{{ config('site.support_email') }}" class="underline">{{ config('site.support_email') }}</a>.</p>
            </section>
        </div>

        <p class="mt-10">
            <a href="{{ route('home') }}" class="text-xs font-semibold uppercase tracking-wider text-neutral-600 underline hover:text-black">На главную</a>
        </p>
    </article>
@endsection
