@extends('layouts.admin')

@section('title', 'Отзывы')
@section('heading', 'Модерация отзывов')

@section('content')
    <p class="mb-6 text-sm text-neutral-600">На проверке: <strong>{{ $pendingCount }}</strong></p>

    <div class="space-y-4">
        @forelse($reviews as $review)
            <article class="rounded-xl border border-neutral-200 bg-white p-4 sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-medium">{{ $review->product?->name }}</p>
                        <p class="text-xs text-neutral-500">{{ $review->user?->name }} · {{ $review->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-amber-500">{{ str_repeat('★', $review->rating) }}</span>
                        @php
                            $statusClass = match($review->status) {
                                'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                default => 'bg-amber-50 text-amber-800 border-amber-200',
                            };
                        @endphp
                        <span class="rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase {{ $statusClass }}">{{ $review->status }}</span>
                    </div>
                </div>
                @if($review->body)
                    <p class="mt-3 text-sm leading-relaxed text-neutral-700">{{ $review->body }}</p>
                @endif
                <div class="mt-4 flex flex-wrap gap-2">
                    @if($review->status === 'pending')
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">@csrf @method('PATCH')
                            <button type="submit" class="rounded-lg bg-black px-3 py-1.5 text-xs font-semibold uppercase text-white">Опубликовать</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Удалить отзыв?');">@csrf @method('DELETE')
                        <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs text-rose-700">Удалить</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="text-sm text-neutral-500">Отзывов пока нет.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $reviews->links() }}</div>
@endsection
