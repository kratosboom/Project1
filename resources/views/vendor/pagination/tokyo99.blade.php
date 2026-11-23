{{-- Pagination gelap-emas — sesuai tema landing --}}
@if ($paginator->hasPages())
    @php
        $pgBase = 'inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-full px-3 text-sm font-medium tabular-nums transition';
        $pgIdle = $pgBase.' border border-white/10 text-[color:var(--ink)] hover:border-[color:var(--gold)] hover:text-[color:var(--gold)]';
        $pgOff = $pgBase.' border border-white/5 text-white/25';
    @endphp
    <nav role="navigation" aria-label="Navigasi halaman" class="w-full max-w-full">
        {{-- Mobile --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="{{ $pgOff }} px-5">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $pgIdle }} px-5">Sebelumnya</a>
            @endif

            <span class="text-xs text-[color:var(--muted)]">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $pgIdle }} px-5">Berikutnya</a>
            @else
                <span class="{{ $pgOff }} px-5">Berikutnya</span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden items-center justify-between gap-6 sm:flex">
            <p class="text-sm text-[color:var(--muted)]">
                @if ($paginator->firstItem())
                    Menampilkan
                    <span class="font-semibold text-[color:var(--ink)]">{{ $paginator->firstItem() }}</span>
                    –
                    <span class="font-semibold text-[color:var(--ink)]">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-[color:var(--ink)]">{{ $paginator->total() }}</span>
                    game
                @else
                    {{ $paginator->count() }} game
                @endif
            </p>

            <div class="flex items-center gap-1.5">
                @if ($paginator->onFirstPage())
                    <span class="{{ $pgOff }}" aria-hidden="true"><i class="fas fa-chevron-left text-xs"></i></span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $pgIdle }}" aria-label="Halaman sebelumnya"><i class="fas fa-chevron-left text-xs" aria-hidden="true"></i></a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2 text-sm text-[color:var(--muted)]">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $pgBase }} btn-primary">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $pgIdle }}" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $pgIdle }}" aria-label="Halaman berikutnya"><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></a>
                @else
                    <span class="{{ $pgOff }}" aria-hidden="true"><i class="fas fa-chevron-right text-xs"></i></span>
                @endif
            </div>
        </div>
    </nav>
@endif
