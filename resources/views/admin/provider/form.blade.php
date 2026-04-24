@extends('layouts.admin')

@php
    $isEdit = $gameProvider->exists;
@endphp

@section('title', ($isEdit ? 'Edit provider' : 'Provider baru') . ' — ' . config('app.name'))

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">{{ $isEdit ? 'Edit provider' : 'Provider baru' }}</h1>
    <a href="{{ route('admin.provider.index') }}" class="mt-2 inline-block text-sm text-primary hover:underline">← Kembali</a>
</div>

<div class="glass max-w-2xl rounded-2xl p-6 sm:p-8">
    <form method="post" action="{{ $isEdit ? route('admin.provider.update', $gameProvider) : route('admin.provider.store') }}" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Nama</label>
            <input type="text" name="name" value="{{ old('name', $gameProvider->name) }}" required class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100" placeholder="PRAGMATIC">
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Icon Font Awesome (class)</label>
            <input type="text" name="icon_class" value="{{ old('icon_class', $gameProvider->icon_class) }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100" placeholder="fa-crown">
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_hot_games" value="0">
            <input type="checkbox" name="is_hot_games" value="1" id="is_hot_games" class="h-4 w-4 rounded border-white/20" {{ old('is_hot_games', $gameProvider->is_hot_games) ? 'checked' : '' }}>
            <label for="is_hot_games" class="text-sm text-slate-300">Tampilkan sebagai tab "HOT GAMES"</label>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">URL logo (langsung ke gambar)</label>
            <input type="url" name="logo_url" value="{{ old('logo_url', $gameProvider->logo_url) }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100" placeholder="https://...">
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Website (referensi &amp; ambil logo)</label>
            <input type="url" name="website_url" value="{{ old('website_url', $gameProvider->website_url) }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100" placeholder="https://">
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Urutan</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $gameProvider->sort_order) }}" min="0" class="w-32 rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-black">Simpan</button>
            <a href="{{ route('admin.provider.index') }}" class="rounded-xl border border-white/15 px-6 py-2.5 text-slate-300">Batal</a>
        </div>
    </form>

    @if($isEdit)
        <div class="mt-8 border-t border-white/10 pt-6">
            <h2 class="mb-2 text-sm font-bold text-white">Ambil logo otomatis</h2>
            <p class="mb-3 text-xs text-gray-500">Kirim URL website — sistem mencoba og:image atau favicon. Simpan dulu "Website" di atas bila perlu, atau salin URL ke bawah.</p>
            <form method="post" action="{{ route('admin.provider.fetchLogo', $gameProvider) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @csrf
                <input type="url" name="website_url" value="{{ old('website_url', $gameProvider->website_url) }}" required class="min-w-0 flex-1 rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100" placeholder="https://">
                <button type="submit" class="whitespace-nowrap rounded-xl border border-amber-500/40 bg-amber-500/10 px-4 py-2.5 text-sm font-bold text-amber-200 hover:bg-amber-500/20">Ambil logo</button>
            </form>
        </div>

        <div class="mt-6">
            <form method="post" action="{{ route('admin.provider.destroy', $gameProvider) }}" onsubmit="return confirm('Hapus provider ini? Game terkait ikut terhapus.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-rose-400 hover:underline">Hapus provider</button>
            </form>
        </div>
    @endif
</div>
@endsection
