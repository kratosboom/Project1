@extends('layouts.admin')

@section('title', 'Testimoni & bukti — '.config('app.name'))

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Testimoni &amp; bukti jackpot</h1>
        <p class="mt-1 text-sm text-gray-400">Konten untuk halaman /testimoni dan /bukti-jackpot.</p>
    </div>
    <a href="{{ route('admin.testimoni.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-black">+ Testimoni</a>
</div>

<div class="space-y-4">
    @forelse($testimonies as $t)
        <div class="glass rounded-2xl border border-white/10 p-5">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="font-bold text-white">{{ $t->author_name }}</p>
                    <p class="text-sm text-gray-500">{{ $t->author_role }}</p>
                    <p class="mt-2 text-sm text-gray-300 line-clamp-2">{{ $t->body }}</p>
                    <p class="mt-2 text-xs text-gray-600">Rating {{ $t->rating }}/5 · urutan {{ $t->sort_order }} · bukti: {{ is_array($t->proof_images) ? count($t->proof_images) : 0 }} gambar</p>
                </div>
                <a href="{{ route('admin.testimoni.edit', $t) }}" class="shrink-0 text-sm text-primary hover:underline">Edit</a>
            </div>
        </div>
    @empty
        <p class="text-gray-500">Belum ada testimoni. Tambah dari tombol di atas (atau jalankan seeder).</p>
    @endforelse
</div>
@endsection
