@if ($paginator->hasPages())
    <nav class="mt-10 flex flex-col items-center gap-4 sm:flex-row sm:justify-between" aria-label="Навигация по страницам">
        <p class="text-xs text-neutral-500">
            Страница {{ $paginator->currentPage() }} из {{ $paginator->lastPage() }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-h-10 min-w-10 items-center justify-center border border-neutral-200 px-3 text-xs text-neutral-300" aria-disabled="true">←</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex min-h-10 min-w-10 items-center justify-center border border-neutral-300 px-3 text-xs font-semibold uppercase tracking-wider hover:bg-neutral-50" rel="prev" aria-label="Предыдущая">←</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-xs text-neutral-400">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex min-h-10 min-w-10 items-center justify-center border border-black bg-black px-3 text-xs font-semibold text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex min-h-10 min-w-10 items-center justify-center border border-neutral-300 px-3 text-xs hover:bg-neutral-50">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex min-h-10 min-w-10 items-center justify-center border border-neutral-300 px-3 text-xs font-semibold uppercase tracking-wider hover:bg-neutral-50" rel="next" aria-label="Следующая">→</a>
            @else
                <span class="inline-flex min-h-10 min-w-10 items-center justify-center border border-neutral-200 px-3 text-xs text-neutral-300" aria-disabled="true">→</span>
            @endif
        </div>
    </nav>
@endif
