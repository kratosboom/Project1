@extends('layouts.app')

@section('title', 'Testimoni — '.config('app.name'))

@section('content')
<div class="mb-10 text-center sm:text-left">
    <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Testimoni</h1>
    <p class="mt-2 max-w-2xl text-slate-400">Cerita singkat dari mereka yang (dalam demo ini) antusias — dengan kartu bergaya editorial dan sedikit sentuhan warna.</p>
    <p class="mt-3">
        <a href="{{ route('bukti_jackpot') }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:underline">
            <i class="fas fa-images" aria-hidden="true"></i>
            Lihat Bukti Jackpot
        </a>
    </p>
</div>

@if($testimonies->isEmpty())
    <div class="glass rounded-2xl p-10 text-center text-slate-400">
        <p>Belum ada testimoni. Jalankan: <code class="text-primary">php artisan db:seed</code> atau tambahkan lewat tinker / migrasi seeder.</p>
    </div>
@else
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($testimonies as $t)
            <article class="group relative overflow-hidden rounded-2xl border border-white/10 bg-gradient-to-br from-slate-800/50 to-slate-900/80 p-6 shadow-xl transition duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-primary/10">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-lg font-bold text-white">{{ $t->author_name }}</p>
                        @if($t->author_role)
                            <p class="text-sm text-slate-400">{{ $t->author_role }}</p>
                        @endif
                    </div>
                    <div class="flex gap-0.5 text-amber-300" aria-hidden="true">
                        @for($i = 0; $i < min(5, (int) $t->rating); $i++)
                            <span>★</span>
                        @endfor
                    </div>
                </div>
                <p class="text-sm leading-relaxed text-slate-300">“{{ $t->body }}”</p>
            </article>
        @endforeach
    </div>
@endif
@endsection
