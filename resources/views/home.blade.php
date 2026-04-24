@extends('layouts.tokyo99')

@php
    $brand = $site['brand_name'] ?? config('app.name');
    $hero = $site['hero_banner_url'] ?? null;
@endphp

@section('title', $brand.' — RTP & demo')

@section('content')
    <div class="mb-10 overflow-hidden rounded-2xl border border-white/5 shadow-2xl">
        <img
            src="{{ $hero ?? 'https://placehold.co/1200x320/1f2937/fb2323?text='.rawurlencode($brand) }}"
            alt="Banner"
            class="h-auto w-full object-cover"
            width="1200"
            height="320"
        >
    </div>

    <div class="-mx-4 overflow-x-auto px-4 pb-4 custom-scrollbar md:mx-0 md:px-0">
        <div class="provider-grid pr-4" id="categoryNav" role="tablist" aria-label="Filter provider">
            @if($providers->isNotEmpty())
                <a
                    href="{{ route('home') }}"
                    class="category-btn font-black uppercase whitespace-nowrap shadow-md transition-all active:scale-95 {{ $gamesShowAll ? 'active' : '' }}"
                    role="tab"
                    @if($gamesShowAll) aria-current="page" @endif
                >
                    <i class="fas fa-border-all" aria-hidden="true"></i>
                    Semua
                </a>
            @endif
            @forelse($providers as $p)
                @php
                    $isActive = ! ($gamesShowAll ?? false) && isset($activeProvider) && $activeProvider->is($p);
                @endphp
                <a
                    href="{{ route('home', ['provider' => $p->slug]) }}"
                    class="category-btn font-black uppercase whitespace-nowrap shadow-md transition-all active:scale-95 {{ $isActive ? 'active' : '' }}"
                    role="tab"
                    @if($isActive) aria-current="page" @endif
                >
                    <i class="fas {{ $p->icon_class ?? 'fa-gamepad' }}" aria-hidden="true"></i>
                    {{ $p->navLabel() }}
                </a>
            @empty
                <span class="text-xs text-gray-500">Tambah provider di admin.</span>
            @endforelse
        </div>
    </div>

    <div class="mb-8 space-y-4">
        <div class="group relative">
            <input type="search" id="gameSearch" placeholder="Cari game berdasarkan nama..." class="w-full rounded-xl border border-white/10 bg-black/40 py-4 pl-12 pr-4 text-sm text-gray-200 placeholder-gray-500 transition-all focus:outline-none focus:ring-2 focus:ring-primary/50" autocomplete="off">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 transition-colors group-focus-within:text-primary"></i>
        </div>
        <div class="flex flex-col items-center justify-between gap-4 rounded-xl border border-white/10 bg-black/40 p-4 md:flex-row md:p-3">
            <div class="w-full overflow-hidden md:flex-1">
                <marquee class="text-xs font-medium text-gray-400">
                    {{ $site['marquee_text'] ?? __('Selamat datang. Jam & pola hanya contoh tata letak; kelola teks di admin pengaturan.') }}
                </marquee>
            </div>
            <div class="flex w-full shrink-0 items-center justify-center gap-3 border-t border-white/5 pt-3 md:w-auto md:justify-end md:border-t-0 md:pt-0">
                <span class="whitespace-nowrap text-[10px] font-black uppercase tracking-widest text-gray-500">Tampilkan:</span>
                <label class="sr-only" for="gameSort">Urutkan</label>
                <select id="gameSort" class="min-w-[140px] cursor-pointer rounded-lg border border-white/10 bg-black/60 py-2 px-4 text-xs text-gray-300 focus:outline-none focus:ring-1 focus:ring-primary/50">
                    <option value="rtp_high">RTP Tertinggi</option>
                    <option value="rtp_low">RTP Terendah</option>
                    <option value="az">A-Z</option>
                    <option value="za">Z-A</option>
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6" id="gamesGrid">
        @forelse($games as $g)
            @php
                $pola = $g->pola ?? ['turbo' => '20X', 'auto' => '50X', 'manual' => '100X'];
                $isBest = $g->is_best;
                $modal = $g->modal_data ?? [];
                $mKap = (int) ($modal['kapital'] ?? 50000);
                $mPred = (int) ($modal['prediksi'] ?? round($mKap * 38.5 * ($g->rtp / 100)));
                $mWr = $modal['winrate'] ?? round(min(98.5, (float) $g->rtp - 1.2 + (crc32($g->name) % 7) * 0.15), 1);
                $mStars = (int) min(5, max(1, $modal['stars'] ?? 5));
            @endphp
            <div class="game-card group relative flex h-full flex-col overflow-hidden rounded-2xl border border-white/10 bg-[#111827] transition-all hover:-translate-y-2 hover:shadow-2xl" data-name="{{ $g->name }}" data-rtp="{{ $g->rtp }}" data-provider-id="{{ $g->game_provider_id }}" data-is-hot="{{ $g->is_hot ? '1' : '0' }}">
                <div class="group group/image relative aspect-square overflow-hidden rounded-t-2xl bg-gray-900">
                    @if($isBest)
                        <div class="pointer-events-none absolute left-2 top-2 z-30 -rotate-12">
                            <i class="fas fa-crown absolute -top-2 left-1/2 z-10 -translate-x-1/2 text-[11px] drop-shadow" style="color: var(--primary);" aria-hidden="true"></i>
                            <span class="mt-1 inline-block rounded border px-2 py-0.5 text-[9px] font-black uppercase tracking-tighter text-white shadow-md" style="border-color: color-mix(in srgb, var(--primary) 30%, transparent); background: color-mix(in srgb, var(--secondary) 88%, black 12%);">BEST</span>
                        </div>
                    @elseif($g->is_hot)
                        <div class="pointer-events-none absolute -left-1 -top-1 z-30 animate-pulse-zoom">
                            <span class="inline-flex h-8 items-center rounded bg-primary px-2 text-[10px] font-black text-black shadow-lg shadow-primary/30">HOT</span>
                        </div>
                    @endif
                    <img src="{{ $g->image_url }}" alt="{{ $g->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" onerror="this.onerror=null;this.src='https://placehold.co/400x400/111827/ffffff?text={{ urlencode($g->name) }}'">
                    <div class="absolute inset-0 hidden items-center justify-center bg-black/60 opacity-0 transition-opacity duration-300 group-hover/image:opacity-100 md:flex">
                        <a href="#" class="btn-primary transform translate-y-4 rounded-xl px-6 py-2.5 text-[11px] font-black uppercase tracking-widest shadow-2xl transition-transform duration-300 group-hover/image:translate-y-0" rel="nofollow" onclick="return false;">Main Sekarang</a>
                    </div>
                </div>
                <div class="flex min-h-0 flex-1 flex-col p-4 text-center sm:p-5">
                    <h4 class="mb-0.5 truncate text-[12px] font-black uppercase leading-tight tracking-tight text-white sm:text-[13px]">{{ $g->name }}</h4>
                    <p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">{{ $g->provider?->name ?? '—' }}</p>
                    <div class="group/bar relative mb-4 h-7 overflow-hidden rounded-full border border-white/10 bg-black/50 shadow-lg ring-1 ring-white/5">
                        <div class="bar-fill-anim relative h-full overflow-hidden rounded-full" style="width: {{ min(100, $g->rtp) }}%; background: linear-gradient(90deg, var(--secondary), var(--primary)); box-shadow: 0 0 16px color-mix(in srgb, var(--primary) 38%, transparent);">
                            <div class="shimmer-effect absolute inset-0 h-full w-full"></div>
                        </div>
                        <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                            <span class="text-[11px] font-black text-white drop-shadow-[0_1px_2px_rgba(0,0,0,0.85)]">{{ number_format($g->rtp, 2) }}%</span>
                        </div>
                    </div>
                    <div class="mb-1.5 flex items-center justify-center gap-2">
                        <div class="h-px min-w-0 flex-1 bg-gradient-to-r from-transparent to-gray-600/50"></div>
                        <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-gray-500">JAM GACOR</span>
                        <div class="h-px min-w-0 flex-1 bg-gradient-to-l from-transparent to-gray-600/50"></div>
                    </div>
                    <p class="mb-4 text-[11px] font-black uppercase tracking-widest text-white sm:text-[12px]">{{ $g->jam_gacor }}</p>
                    <div class="mb-1.5 flex items-center justify-center gap-2">
                        <div class="h-px min-w-0 flex-1 bg-gradient-to-r from-transparent to-gray-600/50"></div>
                        <span class="shrink-0 text-[8px] font-black uppercase tracking-widest text-gray-500">POLA GACOR</span>
                        <div class="h-px min-w-0 flex-1 bg-gradient-to-l from-transparent to-gray-600/50"></div>
                    </div>
                    <div class="mb-4 rounded-xl border border-white/5 bg-black/40 p-2.5 text-left">
                        <div class="flex items-center justify-between gap-2 border-b border-white/5 py-1 text-[8px] font-black uppercase text-white sm:text-[9px]">
                            <span class="text-gray-400">Turbo spin</span>
                            <span class="text-white">{{ $pola['turbo'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2 border-b border-white/5 py-1 text-[8px] font-black uppercase text-white sm:text-[9px]">
                            <span class="text-gray-400">Auto spin</span>
                            <span class="text-white">{{ $pola['auto'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2 py-1 pb-0 text-[8px] font-black uppercase text-white sm:text-[9px]">
                            <span class="text-gray-400">Manual spin</span>
                            <span class="text-white">{{ $pola['manual'] ?? '—' }}</span>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="js-analisis-maxwin mt-auto flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-white/10 bg-gray-900/90 py-2.5 text-[9px] font-black uppercase tracking-widest text-cyan-300/90 transition hover:border-white/20 hover:bg-gray-800 active:scale-[0.98] sm:text-[10px]"
                        data-name="{{ $g->name }}"
                        data-rtp="{{ $g->rtp }}"
                        data-kapital="{{ $mKap }}"
                        data-prediksi="{{ $mPred }}"
                        data-winrate="{{ $mWr }}"
                        data-stars="{{ $mStars }}"
                    >
                        <i class="fas fa-calculator text-gray-500" aria-hidden="true"></i>
                        ANALISIS MAXWIN
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-10 text-center text-sm text-gray-500">
                @if (! $gamesShowAll && isset($activeProvider))
                    <p>Belum ada game untuk provider <strong class="text-white">{{ $activeProvider->name }}</strong>.</p>
                    <p class="mx-auto mt-3 max-w-md text-xs text-gray-600">
                        Kalau baru scrape/impor, sering ada <strong class="text-gray-400">dua baris provider</strong> (mis. <code class="text-gray-500">microgaming</code> vs <code class="text-gray-500">micro-gaming</code>) — game terpasang ke salah satu saja. Gabungkan atau hapus provider kosong di Admin → Provider.
                    </p>
                @else
                    <p>Belum ada game. Tambah dari admin → Game, atau jalankan <code class="text-gray-400">php artisan db:seed --class=SiteContentSeeder</code>.</p>
                @endif
            </div>
        @endforelse
    </div>

    @if ($games->hasPages())
        <div class="mt-10 w-full max-w-7xl">
            {{ $games->onEachSide(1)->links('vendor.pagination.tokyo99') }}
        </div>
    @endif

    {{-- Modal Analisis Maxwin --}}
    <div
        id="maxwinModal"
        class="fixed inset-0 z-[200] hidden items-end justify-center p-0 sm:items-center sm:p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="maxwinModalTitle"
        aria-hidden="true"
    >
        <button type="button" class="js-maxwin-backdrop absolute inset-0 bg-black/75 backdrop-blur-[2px]" aria-label="Tutup"></button>
        <div class="relative z-10 flex max-h-[min(100dvh,720px)] w-full max-w-md flex-col overflow-y-auto rounded-t-3xl border border-white/10 bg-[#111827] p-5 shadow-[0_0_48px_rgba(59,130,246,0.18)] sm:rounded-3xl sm:p-6">
            <div class="relative mb-5 pr-10 text-left">
                <button
                    type="button"
                    class="js-maxwin-close absolute right-0 top-0 flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-gray-800/80 text-white transition hover:bg-gray-700"
                    aria-label="Tutup"
                >
                    <i class="fas fa-xmark text-sm" aria-hidden="true"></i>
                </button>
                <h2 id="maxwinModalTitle" class="text-base font-black uppercase leading-snug tracking-tight text-white sm:text-lg">
                    <span id="maxwinGameTitle">—</span>
                </h2>
                <p id="maxwinRtpLine" class="mt-2 text-xs font-black uppercase tracking-wide text-primary sm:text-sm">
                    REALTIME RTP LIVE: <span id="maxwinRtpVal">0.00</span>%
                </p>
            </div>

            <div class="mb-4">
                <p class="mb-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Modal kapital (IDR)</p>
                <div class="rounded-2xl border border-white/10 bg-black/50 py-4 text-center">
                    <span id="maxwinKapital" class="text-2xl font-black tabular-nums text-white">0</span>
                </div>
            </div>

            <div class="relative mb-4 overflow-hidden rounded-2xl border border-white/10 bg-black/50 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-500">Prediksi kemenangan</p>
                        <p id="maxwinPrediksi" class="text-xl font-black tabular-nums text-white sm:text-2xl">IDR 0</p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/5 text-gray-500">
                        <i class="fas fa-coins text-lg" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="mb-5 grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/10 bg-black/40 p-3 text-center sm:p-4">
                    <p class="mb-1 text-[9px] font-bold uppercase tracking-widest text-gray-500">Win rate</p>
                    <p id="maxwinWinrate" class="text-lg font-black text-primary sm:text-xl">0%</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-black/40 p-3 text-center sm:p-4">
                    <p class="mb-1 text-[9px] font-bold uppercase tracking-widest text-gray-500">Difficulty</p>
                    <div id="maxwinStars" class="flex justify-center gap-0.5 text-primary/80" aria-hidden="true"></div>
                </div>
            </div>

            <a
                href="#"
                class="js-maxwin-cta btn-primary mb-5 flex w-full items-center justify-center gap-2 rounded-2xl py-4 text-sm font-black uppercase tracking-widest shadow-lg shadow-primary/30"
                rel="nofollow"
                onclick="return false;"
            >
                <i class="fas fa-bolt" aria-hidden="true"></i>
                HAJAR SEKARANG
            </a>

            <p class="text-center text-[9px] font-bold uppercase tracking-widest text-gray-600">
                Engine powered by brand analytics
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const input = document.getElementById('gameSearch');
            const grid = document.getElementById('gamesGrid');
            if (!input || !grid) return;
            input.addEventListener('input', function () {
                const q = (this.value || '').toLowerCase().trim();
                grid.querySelectorAll('.game-card').forEach(function (card) {
                    const name = (card.getAttribute('data-name') || '').toLowerCase();
                    card.style.display = !q || name.indexOf(q) !== -1 ? '' : 'none';
                });
            });
            const sort = document.getElementById('gameSort');
            if (sort) {
                sort.addEventListener('change', function () {
                    const cards = Array.from(grid.querySelectorAll('.game-card'));
                    const mode = this.value;
                    cards.sort(function (a, b) {
                        const ra = parseFloat(a.getAttribute('data-rtp') || 0);
                        const rb = parseFloat(b.getAttribute('data-rtp') || 0);
                        const na = (a.getAttribute('data-name') || '').toLowerCase();
                        const nb = (b.getAttribute('data-name') || '').toLowerCase();
                        if (mode === 'rtp_high') return rb - ra;
                        if (mode === 'rtp_low') return ra - rb;
                        if (mode === 'az') return na.localeCompare(nb);
                        if (mode === 'za') return nb.localeCompare(na);
                        return 0;
                    });
                    cards.forEach(function (c) { grid.appendChild(c); });
                });
            }
        })();
        (function () {
            const modal = document.getElementById('maxwinModal');
            if (!modal) return;

            function fmtIdr(n) {
                const num = Math.max(0, parseInt(n, 10) || 0);
                return 'IDR ' + new Intl.NumberFormat('id-ID').format(num);
            }

            function openMaxwin(d) {
                const titleEl = document.getElementById('maxwinGameTitle');
                const rtpVal = document.getElementById('maxwinRtpVal');
                const kapitalEl = document.getElementById('maxwinKapital');
                const prediksiEl = document.getElementById('maxwinPrediksi');
                const winrateEl = document.getElementById('maxwinWinrate');
                const starsEl = document.getElementById('maxwinStars');
                if (!titleEl || !rtpVal || !kapitalEl || !prediksiEl || !winrateEl || !starsEl) return;

                const rtp = parseFloat(d.rtp, 10);
                titleEl.textContent = (d.name || '—').toUpperCase();
                rtpVal.textContent = (isNaN(rtp) ? 0 : rtp).toFixed(2);
                kapitalEl.textContent = new Intl.NumberFormat('id-ID').format(parseInt(d.kapital, 10) || 0);
                prediksiEl.textContent = fmtIdr(d.prediksi);
                const wr = parseFloat(d.winrate, 10);
                winrateEl.textContent = (isNaN(wr) ? 0 : wr).toFixed(1) + '%';

                const stars = Math.min(5, Math.max(1, parseInt(d.stars, 10) || 5));
                starsEl.innerHTML = '';
                for (var s = 0; s < 5; s++) {
                    var icon = document.createElement('i');
                    icon.setAttribute('aria-hidden', 'true');
                    icon.className = 'fas fa-star text-sm ' + (s < stars ? 'text-amber-400' : 'text-gray-600');
                    starsEl.appendChild(icon);
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeMaxwin() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('.js-analisis-maxwin').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openMaxwin(btn.dataset);
                });
            });

            modal.querySelectorAll('.js-maxwin-close, .js-maxwin-backdrop').forEach(function (el) {
                el.addEventListener('click', closeMaxwin);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeMaxwin();
            });
        })();
    </script>
@endpush
