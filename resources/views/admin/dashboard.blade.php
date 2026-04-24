@extends('layouts.admin')

@section('title', 'Ringkasan — '.config('app.name'))

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Ringkasan</h1>
    <p class="mt-1 text-sm text-gray-400">Aktivitas singkat situs dan akses cepat ke pengelolaan konten.</p>
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <a href="{{ route('admin.pengaturan.edit') }}" class="group glass rounded-2xl border border-white/10 p-5 transition hover:border-primary/30">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pengaturan</p>
        <p class="mt-2 text-sm font-medium text-white">Brand, logo, hero, footer, login</p>
        <p class="mt-3 text-sm text-primary group-hover:underline">Buka →</p>
    </a>
    <a href="{{ route('admin.game.index') }}" class="group glass rounded-2xl border border-white/10 p-5 transition hover:border-primary/30">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Game</p>
        <p class="mt-2 text-3xl font-extrabold text-white tabular-nums">{{ $gameCount ?? 0 }}</p>
        <p class="mt-3 text-sm text-primary group-hover:underline">Kelola &amp; impor →</p>
    </a>
    <a href="{{ route('admin.provider.index') }}" class="group glass rounded-2xl border border-white/10 p-5 transition hover:border-primary/30">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Provider</p>
        <p class="mt-2 text-3xl font-extrabold text-white tabular-nums">{{ $providerCount ?? 0 }}</p>
        <p class="mt-3 text-sm text-primary group-hover:underline">Kelola →</p>
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
    <a href="{{ route('admin.testimoni.index') }}" class="group glass rounded-2xl border border-white/10 p-5 transition hover:border-primary/30">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Testimoni</p>
        <p class="mt-2 text-3xl font-extrabold text-white tabular-nums">{{ $testimonyCount ?? 0 }}</p>
        <p class="mt-3 text-sm text-primary group-hover:underline">Bukti &amp; teks →</p>
    </a>
    <a href="{{ route('admin.halaman.create') }}" class="group flex flex-col justify-center glass rounded-2xl border border-dashed border-white/15 p-5 text-center transition hover:border-primary/40 hover:bg-primary/5 sm:col-span-2 lg:col-span-1">
        <span class="text-2xl font-bold text-primary">+</span>
        <span class="mt-1 text-sm font-semibold text-white">Halaman baru</span>
        <span class="mt-0.5 text-xs text-gray-500">Konten &amp; SEO</span>
    </a>
</div>
@endsection
