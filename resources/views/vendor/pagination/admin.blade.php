{{-- Pagination admin: jarak jelas, satu baris scroll jika perlu --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="w-full">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
            <p class="shrink-0 text-sm text-gray-500">
                @if ($paginator->firstItem())
                    Menampilkan
                    <span class="font-medium text-gray-300">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-medium text-gray-300">{{ $paginator->total() }}</span>
                @else
                    {{ $paginator->count() }} data
                @endif
            </p>

            <div class="w-full min-w-0 sm:max-w-[min(100%,42rem)] sm:self-end">
                <div
                    class="flex items-stretch justify-start gap-2 overflow-x-auto rounded-2xl border border-white/10 bg-white/[0.04] p-2 sm:justify-end [-ms-overflow-style:none] [scrollbar-width:thin] [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-white/20"
                >
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex h-10 min-w-10 shrink-0 items-center justify-center rounded-lg border border-white/5 bg-white/[0.03] text-gray-600" aria-hidden="true" title="Halaman pertama">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a
                            href="{{ $paginator->previousPageUrl() }}"
                            rel="prev"
                            class="inline-flex h-10 min-w-10 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/[0.06] text-gray-200 transition hover:border-primary/35 hover:bg-primary/10 hover:text-primary"
                            title="Sebelumnya"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="inline-flex h-10 min-w-10 shrink-0 items-center justify-center px-1 text-sm font-medium text-gray-500">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span
                                        class="inline-flex h-10 min-w-[2.75rem] shrink-0 items-center justify-center rounded-lg bg-primary/18 px-3 text-sm font-bold text-primary ring-1 ring-inset ring-primary/45"
                                        aria-current="page"
                                    >
                                        {{ $page }}
                                    </span>
                                @else
                                    <a
                                        href="{{ $url }}"
                                        class="inline-flex h-10 min-w-[2.75rem] shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/[0.05] px-3 text-sm font-medium text-gray-200 transition hover:border-primary/30 hover:bg-white/[0.1] hover:text-white"
                                    >
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a
                            href="{{ $paginator->nextPageUrl() }}"
                            rel="next"
                            class="inline-flex h-10 min-w-10 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/[0.06] text-gray-200 transition hover:border-primary/35 hover:bg-primary/10 hover:text-primary"
                            title="Berikutnya"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="inline-flex h-10 min-w-10 shrink-0 items-center justify-center rounded-lg border border-white/5 bg-white/[0.03] text-gray-600" aria-hidden="true" title="Terakhir">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </nav>
@endif
