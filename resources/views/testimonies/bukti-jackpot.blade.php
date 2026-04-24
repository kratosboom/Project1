@extends('layouts.app')

@section('title', 'Bukti Jackpot — '.config('app.name'))

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="text-center sm:text-left">
        <p class="text-xs font-black uppercase tracking-widest text-primary">Bukti Jackpot</p>
        <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Bukti Jackpot</h1>
        <p class="mt-2 max-w-2xl text-slate-400">Testimoni dengan bukti gambar (screenshot transfer, struk, dll.) — demo memakai placeholder; nanti bisa diganti URL asli dari penyimpanan Anda.</p>
    </div>
    <div class="flex flex-wrap justify-center gap-2 sm:justify-end">
        <a href="{{ route('testimoni') }}" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-slate-300 transition hover:border-primary/30 hover:text-white">Testimoni (teks saja)</a>
        <a href="{{ route('home') }}" class="rounded-xl border border-primary/40 bg-primary/10 px-4 py-2 text-sm font-bold text-primary transition hover:bg-primary/20">← Beranda</a>
    </div>
</div>

@if($testimonies->isEmpty())
    <div class="glass rounded-2xl p-10 text-center text-slate-400">
        <p>Belum ada testimoni. Jalankan: <code class="text-primary">php artisan db:seed</code></p>
    </div>
@else
    <div class="grid gap-8 lg:grid-cols-2">
        @foreach($testimonies as $t)
            <article class="overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/60 to-slate-900/90 shadow-xl">
                <div class="border-b border-white/5 p-5 sm:p-6">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-lg font-bold text-white">{{ $t->author_name }}</p>
                            @if($t->author_role)
                                <p class="text-sm text-slate-400">{{ $t->author_role }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 gap-0.5 text-amber-300" aria-hidden="true">
                            @for($i = 0; $i < min(5, (int) $t->rating); $i++)
                                <span>★</span>
                            @endfor
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-300">“{{ $t->body }}”</p>
                </div>

                <div class="bg-black/20 p-5 sm:p-6">
                    <div class="mb-3 flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-500">
                        <i class="fas fa-image text-primary/80" aria-hidden="true"></i>
                        Bukti (gambar)
                    </div>
                    @php
                        $proofs = is_array($t->proof_images) ? $t->proof_images : [];
                    @endphp
                    @if(count($proofs) > 0)
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach($proofs as $src)
                                <a href="{{ $src }}" target="_blank" rel="noopener noreferrer" class="group relative block overflow-hidden rounded-xl border border-white/10 bg-slate-900/50 ring-1 ring-white/5 transition hover:border-primary/40 hover:ring-primary/20">
                                    <div class="aspect-[16/10] w-full">
                                        <img
                                            src="{{ $src }}"
                                            alt="Bukti dari {{ $t->author_name }}"
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                                            loading="lazy"
                                        >
                                    </div>
                                    <span class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-2 py-2 text-[10px] font-bold text-white/90 opacity-0 transition group-hover:opacity-100">Buka ukuran penuh</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="flex min-h-[120px] items-center justify-center rounded-xl border border-dashed border-white/15 bg-slate-900/40 px-4 text-center text-sm text-slate-500">
                            Belum ada gambar bukti untuk entri ini. Isi kolom <code class="text-slate-400">proof_images</code> di database atau jalankan seeder terbaru.
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif
@endsection
