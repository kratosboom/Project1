@extends('layouts.admin')

@section('title', 'Game — '.config('app.name'))

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Game</h1>
        <p class="mt-1 text-sm text-gray-400">Kelola daftar game RTP — scrape HTML atau edit manual.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <form method="post" action="{{ route('admin.game.randomizeAll') }}" onsubmit="return confirm('Acak RTP, Jam Gacor, dan Pola untuk SEMUA game?');">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-amber-400/40 bg-amber-400/10 px-4 py-2.5 text-sm font-bold text-amber-300 hover:bg-amber-400/20">🎲 Acak Semua</button>
        </form>
        <form method="post" action="{{ route('admin.game.resetClicks') }}" onsubmit="return confirm('Reset SEMUA click count ke 0? Status Hot akan mulai lagi dari nol.');">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-rose-400/40 bg-rose-500/10 px-4 py-2.5 text-sm font-bold text-rose-300 hover:bg-rose-500/20">↺ Reset Klik</button>
        </form>
        <a href="{{ route('admin.game.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-black">+ Game</a>
    </div>
</div>

<div class="mb-6 rounded-xl border border-sky-400/20 bg-sky-500/5 px-4 py-3 text-xs text-sky-200">
    <strong>🔥 Hot otomatis dari klik user.</strong> Top {{ \App\Services\ClickTrackerService::HOT_LIMIT }} game dengan <code class="text-sky-300">click_count</code> tertinggi otomatis ditandai Hot. Admin tidak lagi set manual — gunakan <strong>Reset Klik</strong> untuk mulai dari nol.
</div>

@if(session('ok'))
    <div class="mb-6 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('ok') }}</div>
@endif

<div class="mb-8 glass max-w-4xl rounded-2xl p-6 sm:p-8">
    <h2 class="mb-2 text-sm font-bold text-white">Auto-Grabber RTP (scrape)</h2>
    <p class="mb-1 text-xs text-gray-500">Parser mendukung dua format: <code class="text-gray-400">.game-card[data-name][data-rtp]</code> (tokyo99-like) dan <code class="text-gray-400">.game-box[data-title]</code> (laju22-like, hanya nama &amp; gambar).</p>
    <p class="mb-3 text-xs text-gray-500">Isi <strong>URL sumber</strong> — sistem akan fetch otomatis; kategori/provider dibuat otomatis dari segmen URL (bisa diisi manual di bawah). Jika kena <strong>403/Cloudflare</strong>, buka URL di browser lalu paste view-source ke kotak di bawah.</p>
    <form method="post" action="{{ route('admin.game.importHtml') }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-xs font-bold text-gray-400 mb-1">URL sumber</label>
            <input type="url" name="source_url" value="{{ old('source_url') }}" placeholder="https://laju22.net/slots/pragmatic-play" class="w-full rounded-xl border border-white/10 bg-black/40 p-2.5 text-sm text-gray-200">
            @error('source_url')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 mb-1">Nama kategori / provider <span class="text-gray-600 font-normal">(opsional)</span></label>
            <input type="text" name="provider_name" value="{{ old('provider_name') }}" placeholder="Contoh: Pragmatic Play" class="w-full rounded-xl border border-white/10 bg-black/40 p-2.5 text-sm text-gray-200">
            <p class="mt-1 text-[11px] text-gray-500">Jika <strong>kosong</strong> dan URL diisi, kategori baru dibuat otomatis dari segmen terakhir URL. Jika hanya <strong>paste HTML</strong> (tanpa URL), isi nama agar kategori dibuat. Jika diisi, dipakai sebagai nama (slug otomatis).</p>
            @error('provider_name')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 mb-1">Filter <code class="text-gray-500">data-provider</code> <span class="text-gray-600">(opsional)</span></label>
            <input type="text" name="filter_provider" value="{{ old('filter_provider') }}" placeholder="contoh: PRAGMATIC" class="w-full rounded-xl border border-white/10 bg-black/40 p-2.5 text-sm text-gray-200">
            <p class="mt-1 text-[11px] text-gray-600">Isi jika HTML berisi banyak provider.</p>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 mb-1">Mode</label>
            <div class="flex gap-4 text-sm">
                <label class="flex items-center gap-2 text-gray-300">
                    <input type="radio" name="mode" value="upsert" @checked(old('mode', 'upsert') === 'upsert') class="accent-primary">
                    <span>Upsert <span class="text-gray-600">(tambah + update game yang sama)</span></span>
                </label>
                <label class="flex items-center gap-2 text-gray-300">
                    <input type="radio" name="mode" value="fresh" @checked(old('mode') === 'fresh') class="accent-rose-400">
                    <span class="text-rose-300">Fresh <span class="text-gray-600">(hapus semua game provider ini dulu)</span></span>
                </label>
            </div>
            @error('mode')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-400 mb-1">Paste HTML (view-source) <span class="text-gray-600">— hanya jika URL diblokir</span></label>
            <textarea name="html" rows="8" class="w-full rounded-xl border border-white/10 bg-black/40 p-3 font-mono text-[11px] text-gray-200" placeholder="<!DOCTYPE html>...">{{ old('html') }}</textarea>
            @error('html')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" formaction="{{ route('admin.game.previewHtml') }}" class="rounded-xl border border-white/20 bg-white/5 px-4 py-2 text-sm font-bold text-gray-200 hover:bg-white/10">Pratinjau (dry-run)</button>
            <button type="submit" formaction="{{ route('admin.game.importHtml') }}" class="rounded-xl border border-primary/50 bg-primary/10 px-4 py-2 text-sm font-bold text-primary hover:bg-primary/20">Scrape &amp; simpan</button>
        </div>
    </form>
</div>

@isset($previewResult)
    <div class="mb-8 glass max-w-5xl rounded-2xl p-6 sm:p-8 border-2 border-amber-400/40">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-amber-300">Pratinjau hasil scrape (belum disimpan)</h2>
                <p class="text-xs text-gray-400 mt-1">
                    {{ count($previewResult['cards']) }} game terdeteksi untuk provider
                    <span class="font-bold text-white">{{ $previewResult['provider']->name }}</span>
                    @if($previewResult['filter']) · filter: <code class="text-gray-400">{{ $previewResult['filter'] }}</code> @endif
                    · mode: <code class="text-gray-400">{{ $previewResult['mode'] }}</code>
                </p>
            </div>
            <form method="post" action="{{ route('admin.game.importHtml') }}" class="flex gap-2">
                @csrf
                <input type="hidden" name="provider_name" value="{{ $previewResult['provider_name'] ?? '' }}">
                <input type="hidden" name="filter_provider" value="{{ $previewResult['filter'] ?? '' }}">
                <input type="hidden" name="mode" value="{{ $previewResult['mode'] }}">
                <input type="hidden" name="html" value="{{ $previewResult['html'] }}">
                <input type="hidden" name="source_url" value="{{ $previewResult['source_url'] ?? '' }}">
                <button type="submit" class="rounded-xl bg-emerald-500/20 border border-emerald-400/50 px-4 py-2 text-sm font-bold text-emerald-300 hover:bg-emerald-500/30">Lanjutkan simpan &rarr;</button>
            </form>
        </div>
        <div class="overflow-x-auto rounded-xl border border-white/10">
            <table class="w-full min-w-[720px] text-left text-xs">
                <thead class="border-b border-white/10 bg-white/5 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-3 py-2">#</th>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Provider label</th>
                        <th class="px-3 py-2">RTP</th>
                        <th class="px-3 py-2">Hot</th>
                        <th class="px-3 py-2">Best</th>
                        <th class="px-3 py-2">Jam Gacor</th>
                        <th class="px-3 py-2">Pola</th>
                        <th class="px-3 py-2">Image</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-gray-300">
                    @foreach($previewResult['cards'] as $i => $c)
                        <tr>
                            <td class="px-3 py-2 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-white">{{ $c['name'] }}</td>
                            <td class="px-3 py-2">{{ $c['provider_label'] }}</td>
                            <td class="px-3 py-2 tabular-nums">{{ number_format($c['rtp'], 2) }}%</td>
                            <td class="px-3 py-2">{{ $c['is_hot'] ? '✓' : '—' }}</td>
                            <td class="px-3 py-2">{{ $c['is_best'] ? '✓' : '—' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">{{ $c['jam_gacor'] }}</td>
                            <td class="px-3 py-2 font-mono text-[10px]">{{ json_encode($c['pola']) }}</td>
                            <td class="px-3 py-2">
                                @if($c['image_url'])
                                    <a href="{{ $c['image_url'] }}" target="_blank" class="text-primary hover:underline">{{ \Illuminate\Support\Str::limit($c['image_url'], 40) }}</a>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endisset

<div class="mb-4 glass rounded-2xl border border-white/10 p-4 sm:p-5">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-lg font-bold text-white">Daftar Game</h2>
            <p class="mt-0.5 text-sm text-gray-500">
                Total <span class="font-semibold text-gray-300">{{ $games->total() }}</span> game
                @if(! empty($search))
                    · hasil pencarian &ldquo;{{ $search }}&rdquo;
                @endif
            </p>
        </div>
        <form method="get" action="{{ route('admin.game.index') }}" class="flex w-full flex-col gap-2 sm:w-auto sm:min-w-[min(100%,24rem)] sm:flex-row sm:items-stretch" role="search">
            <label for="admin-game-search" class="sr-only">Cari game</label>
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input
                    id="admin-game-search"
                    type="search"
                    name="q"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama game atau provider…"
                    autocomplete="off"
                    class="w-full rounded-xl border border-white/10 bg-black/50 py-2.5 pl-10 pr-4 text-sm text-gray-100 placeholder-gray-500 focus:border-primary/40 focus:outline-none focus:ring-1 focus:ring-primary/30"
                >
            </div>
            <div class="flex gap-2">
                <button type="submit" class="shrink-0 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-black transition hover:opacity-90">Cari</button>
                @if(! empty($search))
                    <a href="{{ route('admin.game.index') }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm text-gray-300 hover:border-white/20 hover:text-white">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="overflow-hidden rounded-2xl border border-white/10">
    <table class="w-full min-w-[800px] text-left text-sm">
        <thead class="border-b border-white/10 bg-white/5 text-xs font-bold uppercase tracking-wider text-gray-500">
            <tr>
                <th class="px-4 py-3 w-16">Foto</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Provider</th>
                <th class="px-4 py-3">RTP</th>
                <th class="px-4 py-3 w-20 text-center">Hot</th>
                <th class="px-4 py-3 w-20 text-right">Klik</th>
                <th class="px-4 py-3">Jam Gacor</th>
                <th class="px-4 py-3 w-32 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($games as $g)
                @php
                    $rtp = (float) $g->rtp;
                    if ($rtp > 80)      { $rtpClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30'; }
                    elseif ($rtp >= 50) { $rtpClass = 'bg-amber-500/20 text-amber-300 border-amber-400/30'; }
                    else                { $rtpClass = 'bg-rose-500/20 text-rose-300 border-rose-400/30'; }
                @endphp
                <tr class="text-gray-300 {{ isset($g->is_active) && ! $g->is_active ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3">
                        @if($g->image_url)
                            <img src="{{ $g->image_url }}" alt="" class="h-12 w-12 rounded-lg border border-white/10 object-cover" onerror="this.style.display='none'">
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-xs font-bold text-gray-500">{{ strtoupper(mb_substr($g->name, 0, 1)) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-white">{{ $g->name }}</span>
                        @if($g->is_best)<span class="ml-2 rounded bg-fuchsia-500/20 px-1.5 py-0.5 text-[10px] font-bold uppercase text-fuchsia-300">Best</span>@endif
                        @if(isset($g->is_active) && ! $g->is_active)<span class="ml-2 rounded bg-gray-500/20 px-1.5 py-0.5 text-[10px] font-bold uppercase text-gray-400">Nonaktif</span>@endif
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $g->provider?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-bold tabular-nums {{ $rtpClass }}">{{ number_format($rtp, 2) }}%</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($g->is_hot)
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-orange-400/50 bg-orange-500/20 text-base shadow-[0_0_12px_rgba(251,146,60,0.25)]" title="Top {{ \App\Services\ClickTrackerService::HOT_LIMIT }} berdasarkan click_count">🔥</span>
                        @else
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-white/5 bg-white/5 text-base opacity-30" title="Belum masuk top klik">🔥</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-xs text-gray-400 tabular-nums">{{ number_format($g->click_count ?? 0) }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400 tabular-nums">{{ $g->jam_gacor }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.game.edit', $g) }}" class="mr-2 text-primary hover:underline">Edit</a>
                        <form method="post" action="{{ route('admin.game.destroy', $g) }}" class="inline" onsubmit="return confirm('Hapus game ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-400 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">{{ ! empty($search) ? 'Tidak ada game yang cocok.' : 'Belum ada game.' }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($games->hasPages())
    <div class="mt-6">
        {{ $games->onEachSide(1)->links('vendor.pagination.admin') }}
    </div>
@endif
@endsection
