@extends('layouts.site')

@php
    $brand = $site['brand_name'] ?? config('app.name');
@endphp

@section('title', $brand.' — Skor Langsung & Jadwal Pertandingan')

@section('content')
    {{-- Beranda sementara: konten livescore akan diisi pada tahap setup awal. --}}
    <section aria-labelledby="hero-title" class="lux-panel relative overflow-hidden rounded-[32px]">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -right-24 -top-32 h-96 w-96 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--secondary) 55%, transparent);"></div>
            <div class="absolute inset-x-10 top-0 h-px" style="background: linear-gradient(90deg, transparent, var(--gold), transparent);"></div>
        </div>
        <div class="relative px-6 py-20 text-center sm:px-12 sm:py-28">
            <p class="ornament mx-auto max-w-xs">Segera hadir</p>
            <h1 id="hero-title" class="mt-6 font-display text-5xl font-semibold leading-tight text-[color:var(--ink)] sm:text-7xl">
                <span class="gold-text">{{ $brand }}</span>
            </h1>
            <p class="mx-auto mt-5 max-w-xl text-base leading-relaxed text-[color:var(--muted)]">
                Skor langsung, jadwal, dan hasil pertandingan — sedang kami siapkan.
            </p>
        </div>
    </section>
@endsection
