@extends('layouts.app')

@section('title', 'Доставка')

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        <x-page-heading title="Доставка" lede="Ваши назначенные заказы" />

        <div class="space-y-5">
            @forelse($orders as $order)
                @php
                    $addr = is_array($order->address) ? ($order->address['full'] ?? null) : null;
                    $clientPhone = $order->user?->phone;
                    $terminal = in_array($order->status, ['cancelled', 'delivered'], true);
                    $statusClass = \App\Support\OrderStatus::badgeClass($order->status);
                @endphp
                <article class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-neutral-100 px-4 py-4 sm:px-5">
                        <div>
                            <p class="text-sm font-semibold tracking-tight">Заказ #{{ $order->id }}</p>
                            <p class="mt-1 text-lg font-semibold tabular-nums">{{ number_format($order->total_price, 0, '.', ' ') }} ₽</p>
                        </div>
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider {{ $statusClass }}">
                            {{ \App\Support\OrderStatus::label($order->status) }}
                        </span>
                    </div>

                    @if($order->leave_at_door)
                        <div class="border-b border-amber-100 bg-gradient-to-r from-amber-50 to-orange-50/60 px-4 py-3.5 sm:px-5">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-amber-700 shadow-sm ring-1 ring-amber-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-amber-950">Оставить у двери</p>
                                    <p class="mt-0.5 text-xs leading-relaxed text-amber-800/90">
                                        @if($terminal && $order->delivery_photo)
                                            Доставка подтверждена фото у двери
                                        @elseif(! $terminal)
                                            Оставьте заказ у двери и приложите фото при завершении
                                        @else
                                            Клиент просил оставить у двери
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3 px-4 py-4 text-sm sm:px-5">
                        @if($order->user)
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-neutral-400">Клиент</p>
                                <p class="mt-1 font-medium">{{ $order->user->name }}</p>
                                @if($clientPhone)
                                    <a href="tel:{{ preg_replace('/\s+/', '', $clientPhone) }}" class="mt-0.5 inline-flex items-center gap-1 text-neutral-600 underline decoration-neutral-300 underline-offset-2 hover:text-black">
                                        {{ $clientPhone }}
                                    </a>
                                @endif
                            </div>
                        @endif

                        @if($addr)
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-neutral-400">Адрес</p>
                                <p class="mt-1 leading-relaxed text-neutral-800">{{ $addr }}</p>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-neutral-400">Состав</p>
                            <ul class="mt-2 space-y-2">
                                @foreach($order->items as $item)
                                    <li class="flex justify-between gap-3">
                                        <span>{{ $item->product?->name ?? 'Товар' }} <span class="text-neutral-500">· {{ $item->size }} · ×{{ $item->quantity }}</span></span>
                                        <span class="shrink-0 font-medium tabular-nums">{{ number_format($item->price * $item->quantity, 0, '.', ' ') }} ₽</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if($order->delivery_photo)
                        <div class="border-t border-neutral-100 px-4 py-4 sm:px-5">
                            <p class="text-xs font-medium uppercase tracking-wider text-neutral-400">Фото доставки</p>
                            <a href="{{ $order->deliveryPhotoUrl() }}" target="_blank" rel="noopener" class="mt-3 block overflow-hidden rounded-xl border border-neutral-200 bg-neutral-50">
                                <img src="{{ $order->deliveryPhotoUrl() }}" alt="Фото доставки заказа #{{ $order->id }}" class="max-h-56 w-full object-cover">
                            </a>
                        </div>
                    @endif

                    @unless($terminal)
                        <div class="border-t border-neutral-100 bg-neutral-50/50 px-4 py-4 sm:px-5">
                            <form method="POST" action="{{ route('courier.orders.arrived', $order) }}" class="mb-3">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-neutral-900 px-4 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-neutral-800">
                                    Я на месте
                                </button>
                            </form>

                            <form method="POST" action="{{ route('courier.orders.delivered', $order) }}" enctype="multipart/form-data" class="space-y-3" data-leave-at-door="{{ $order->leave_at_door ? '1' : '0' }}">
                                @csrf

                                @if($order->leave_at_door)
                                    <div class="rounded-xl border border-dashed border-amber-300/80 bg-white p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-800">Фото у двери</p>
                                        <p class="mt-1 text-xs text-neutral-500">Сфотографируйте заказ у двери клиента — это обязательно для завершения доставки.</p>

                                        <label class="courier-photo-drop mt-4 flex cursor-pointer flex-col items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-8 text-center transition hover:border-neutral-300 hover:bg-white">
                                            <input type="file" name="delivery_photo" accept="image/*" capture="environment" class="sr-only courier-photo-input" @if($order->leave_at_door) required @endif>
                                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-900 text-white">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                                                </svg>
                                            </span>
                                            <span class="courier-photo-label mt-3 text-sm font-medium text-neutral-900">Сделать или выбрать фото</span>
                                            <span class="mt-1 text-xs text-neutral-500">JPG, PNG до 5 МБ</span>
                                        </label>

                                        <div class="courier-photo-preview mt-3 hidden overflow-hidden rounded-xl border border-neutral-200">
                                            <img src="" alt="Предпросмотр" class="max-h-48 w-full object-cover">
                                        </div>

                                        @error('delivery_photo')
                                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif

                                <button type="submit" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-900 transition hover:border-neutral-900">
                                    Доставлено
                                </button>
                            </form>
                        </div>
                    @endunless
                </article>
            @empty
                <x-empty-state icon="cart" title="Нет заказов" description="Когда администратор назначит вам доставку, она появится здесь." />
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('form[data-leave-at-door="1"]').forEach((form) => {
            const input = form.querySelector('.courier-photo-input');
            const drop = form.querySelector('.courier-photo-drop');
            const preview = form.querySelector('.courier-photo-preview');
            const previewImg = preview?.querySelector('img');
            const label = form.querySelector('.courier-photo-label');
            if (!input || !drop || !preview || !previewImg) return;

            input.addEventListener('change', () => {
                const file = input.files?.[0];
                if (!file) {
                    preview.classList.add('hidden');
                    drop.classList.remove('hidden');
                    return;
                }
                previewImg.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                drop.classList.add('hidden');
                if (label) label.textContent = file.name;
            });
        });
    </script>
    @endpush
@endsection
