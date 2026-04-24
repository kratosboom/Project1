{{-- Pagination putih dengan pemisah vertikal — sesuai tema landing --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="w-full max-w-full">
        {{-- Mobile --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-400 shadow-sm">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm transition hover:bg-gray-50">
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm transition hover:bg-gray-50">
                    Berikutnya
                </a>
            @else
                <span class="inline-flex items-center rounded-lg border border-gray-200 bg-white/80 px-4 py-2.5 text-sm font-semibold text-gray-400 shadow-sm">
                    Berikutnya
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden flex-col gap-4 sm:flex sm:flex-row sm:items-center sm:justify-between sm:gap-6">
            <p class="text-sm text-gray-400">
                @if ($paginator->firstItem())
                    Menampilkan
                    <span class="font-semibold text-gray-200">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-semibold text-gray-200">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-semibold text-gray-200">{{ $paginator->total() }}</span>
                    game
                @else
                    {{ $paginator->count() }} game
                @endif
            </p>

            <div class="inline-flex divide-x divide-gray-200 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-2.5 py-2 text-gray-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2.5 py-2 text-gray-700 transition hover:bg-gray-50" aria-label="Halaman sebelumnya">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex min-w-[2.5rem] items-center justify-center bg-gray-200 px-3 py-2 text-sm font-bold text-gray-900">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex min-w-[2.5rem] items-center justify-center bg-white px-3 py-2 text-sm font-semibold text-gray-800 transition hover:bg-gray-50" aria-label="Ke halaman {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2.5 py-2 text-gray-700 transition hover:bg-gray-50" aria-label="Halaman berikutnya">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span class="inline-flex items-center px-2.5 py-2 text-gray-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
