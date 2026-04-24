@extends('layouts.admin')

@php
    $isEdit = $testimony->exists;
    $proofLines = old('proof_images_text');
    if ($proofLines === null) {
        $proof = $testimony->proof_images;
        if (is_array($proof) && $proof !== []) {
            $proofLines = implode("\n", $proof);
        } else {
            $proofLines = '';
        }
    }
@endphp

@section('title', ($isEdit ? 'Edit testimoni' : 'Testimoni baru') . ' — ' . config('app.name'))

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">{{ $isEdit ? 'Edit testimoni' : 'Testimoni baru' }}</h1>
    <a href="{{ route('admin.testimoni.index') }}" class="mt-2 inline-block text-sm text-primary hover:underline">← Kembali</a>
</div>

<div class="glass max-w-2xl rounded-2xl p-6 sm:p-8">
    <form method="post" action="{{ $isEdit ? route('admin.testimoni.update', $testimony) : route('admin.testimoni.store') }}" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Nama</label>
            <input type="text" name="author_name" value="{{ old('author_name', $testimony->author_name) }}" required class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Jabatan / keterangan</label>
            <input type="text" name="author_role" value="{{ old('author_role', $testimony->author_role) }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Isi testimoni</label>
            <textarea name="body" rows="5" required class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">{{ old('body', $testimony->body) }}</textarea>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Rating (1-5)</label>
            <input type="number" name="rating" value="{{ old('rating', $testimony->rating ?? 5) }}" min="1" max="5" required class="w-32 rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Bukti jackpot (satu URL gambar per baris, atau array JSON)</label>
            <textarea name="proof_images_text" rows="4" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 font-mono text-xs text-slate-200" placeholder="https://...">{{ $proofLines }}</textarea>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Urutan</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $testimony->sort_order) }}" min="0" class="w-32 rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-black">Simpan</button>
            <a href="{{ route('admin.testimoni.index') }}" class="rounded-xl border border-white/15 px-6 py-2.5 text-slate-300">Batal</a>
        </div>
    </form>

    @if($isEdit)
        <form method="post" action="{{ route('admin.testimoni.destroy', $testimony) }}" class="mt-8 border-t border-white/10 pt-4" onsubmit="return confirm('Hapus testimoni?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-rose-400 hover:underline">Hapus</button>
        </form>
    @endif
</div>
@endsection
