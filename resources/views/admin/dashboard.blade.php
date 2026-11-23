@extends('layouts.admin')

@section('title', 'Ringkasan — '.config('app.name'))

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Ringkasan</h1>
    <p class="mt-1 text-sm text-gray-400">Aktivitas singkat situs dan akses cepat ke pengelolaan konten.</p>
</div>

<div class="glass mb-6 rounded-2xl border border-white/10 p-5 sm:p-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Livescore · API-Sports</p>
            @if(! $livescore['configured'])
                <p class="mt-2 text-sm text-amber-300">API_SPORTS_KEY belum diisi di file .env server.</p>
            @else
                <p class="mt-2 text-3xl font-extrabold tabular-nums text-white">
                    {{ $livescore['quota']['remaining'] ?? '—' }}<span class="text-base font-medium text-gray-500"> / {{ $livescore['quota']['limit'] ?? config('livescore.quota.daily_limit') }} request tersisa hari ini</span>
                </p>
            @endif
            <p class="mt-2 text-sm text-gray-400">
                {{ number_format($livescore['fixtures']) }} pertandingan tersimpan · {{ $livescore['leagues'] }} liga dipantau ·
                Sinkron terakhir: {{ $livescore['lastSync'] ? $livescore['lastSync']->timezone(config('livescore.timezone'))->locale('id')->translatedFormat('j M Y, H:i').' WIB' : 'belum pernah' }}
            </p>
        </div>
        @if($livescore['configured'])
            <form method="post" action="{{ route('admin.livescore.sync') }}">
                @csrf
                <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-black transition hover:brightness-110">Sinkron skor sekarang</button>
                <p class="mt-1.5 text-[11px] text-gray-500">Memakai 1 request. Maks 2× per 10 menit.</p>
            </form>
        @endif
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <a href="{{ route('admin.pengaturan.edit') }}" class="group glass rounded-2xl border border-white/10 p-5 transition hover:border-primary/30">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pengaturan</p>
        <p class="mt-2 text-sm font-medium text-white">Brand, logo, favicon, footer</p>
        <p class="mt-3 text-sm text-primary group-hover:underline">Buka →</p>
    </a>
    <a href="{{ route('admin.halaman.index') }}" class="group glass rounded-2xl border border-white/10 p-5 transition hover:border-primary/30">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total halaman</p>
        <p class="mt-2 text-3xl font-extrabold text-white tabular-nums">{{ $pageCount }}</p>
        <p class="mt-3 text-sm text-primary group-hover:underline">Kelola →</p>
    </a>
    <div class="glass rounded-2xl border border-white/10 p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Dipublikasikan</p>
        <p class="mt-2 text-3xl font-extrabold text-emerald-300/90 tabular-nums">{{ $publishedCount }}</p>
        <p class="mt-3 text-sm text-gray-500">Terlihat di URL publik</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="group glass rounded-2xl border border-white/10 p-5 transition hover:border-primary/30">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">User admin</p>
        <p class="mt-2 text-3xl font-extrabold text-white tabular-nums">{{ $userCount ?? 0 }}</p>
        <p class="mt-3 text-sm text-primary group-hover:underline">Kelola →</p>
    </a>
    <a href="{{ route('admin.halaman.create') }}" class="group flex flex-col justify-center glass rounded-2xl border border-dashed border-white/15 p-5 text-center transition hover:border-primary/40 hover:bg-primary/5 sm:col-span-2 lg:col-span-1">
        <span class="text-2xl font-bold text-primary">+</span>
        <span class="mt-1 text-sm font-semibold text-white">Halaman baru</span>
        <span class="mt-0.5 text-xs text-gray-500">Konten &amp; SEO</span>
    </a>
</div>
@endsection
