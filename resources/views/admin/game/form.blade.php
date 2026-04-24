@extends('layouts.admin')

@php
    $pola = $game->pola ?? [];
    $isEdit = $game->exists;
@endphp

@section('title', ($isEdit ? 'Edit game' : 'Game baru') . ' — ' . config('app.name'))

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">{{ $isEdit ? 'Edit game' : 'Game baru' }}</h1>
    <a href="{{ route('admin.game.index') }}" class="mt-2 inline-block text-sm text-primary hover:underline">← Kembali</a>
</div>

<div class="glass max-w-2xl rounded-2xl p-6 sm:p-8">
    <form method="post" action="{{ $isEdit ? route('admin.game.update', $game) : route('admin.game.store') }}" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Provider</label>
            <select name="game_provider_id" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100" required>
                @foreach($gameProviders as $gp)
                    <option value="{{ $gp->id }}" @selected(old('game_provider_id', $game->game_provider_id) == $gp->id)>{{ $gp->name }}{{ $gp->is_hot_games ? ' (HOT GAMES tab)' : '' }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Nama game</label>
            <input type="text" name="name" value="{{ old('name', $game->name) }}" required class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">URL gambar</label>
            <input type="url" name="image_url" value="{{ old('image_url', $game->image_url) }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100" placeholder="https://">
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">RTP %</label>
            <input type="number" name="rtp" value="{{ old('rtp', $game->rtp) }}" step="0.01" min="0" max="100" required class="w-full max-w-xs rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">
        </div>

        <div class="flex flex-wrap gap-4">
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="hidden" name="is_best" value="0">
                <input type="checkbox" name="is_best" value="1" class="h-4 w-4" {{ old('is_best', $game->is_best) ? 'checked' : '' }}> Best
            </label>
            @if($isEdit)
                <span class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-black/30 px-3 py-1.5 text-xs text-slate-400">
                    🔥 Hot: <strong class="text-slate-200">{{ $game->is_hot ? 'Ya' : 'Tidak' }}</strong>
                    · {{ number_format($game->click_count ?? 0) }} klik
                </span>
            @endif
        </div>
        <p class="-mt-2 text-[11px] text-slate-500">Status <strong>Hot</strong> dihitung otomatis dari click_count (top {{ \App\Services\ClickTrackerService::HOT_LIMIT }}) — tidak bisa diset manual.</p>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Jam gacor (teks)</label>
            <input type="text" name="jam_gacor" value="{{ old('jam_gacor', $game->jam_gacor) }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100" placeholder="10.15 - 13.45">
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-xs font-medium text-slate-400">Pola turbo</label>
                <input type="text" name="pola_turbo" value="{{ old('pola_turbo', $pola['turbo'] ?? '') }}" class="w-full rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-slate-100" placeholder="20X">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-slate-400">Pola auto</label>
                <input type="text" name="pola_auto" value="{{ old('pola_auto', $pola['auto'] ?? '') }}" class="w-full rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-slate-100" placeholder="50X">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-slate-400">Pola manual</label>
                <input type="text" name="pola_manual" value="{{ old('pola_manual', $pola['manual'] ?? '') }}" class="w-full rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-slate-100" placeholder="100X">
            </div>
        </div>

        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-300">Urutan</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $game->sort_order) }}" min="0" class="w-32 rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100">
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="h-4 w-4" {{ old('is_active', $game->exists ? $game->is_active : true) ? 'checked' : '' }}>
                <span class="font-medium">Status Aktif</span>
            </label>
            <p class="ml-6 mt-1 text-xs text-slate-500">Nonaktifkan untuk menyembunyikan game dari halaman publik.</p>
        </div>

        <div class="rounded-xl border border-white/5 bg-black/20 p-4">
            <h3 class="mb-3 text-sm font-bold text-slate-200">Popup Analisis Maxwin <span class="text-xs font-normal text-slate-500">(opsional)</span></h3>
            <div class="space-y-3">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">Teks footer "Engine"</label>
                    <input type="text" name="maxwin_footer_text" value="{{ old('maxwin_footer_text', $game->maxwin_footer_text) }}" maxlength="280" placeholder="Engine Powered by Brand Analytics" class="w-full rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-slate-100">
                    <p class="mt-1 text-[11px] text-slate-500">Kosongkan untuk memakai teks bawaan situs.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">Difficulty min (1–5)</label>
                        <input type="number" name="maxwin_difficulty_min" value="{{ old('maxwin_difficulty_min', $game->maxwin_difficulty_min) }}" min="1" max="5" placeholder="1" class="w-full rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-slate-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-400">Difficulty max (1–5)</label>
                        <input type="number" name="maxwin_difficulty_max" value="{{ old('maxwin_difficulty_max', $game->maxwin_difficulty_max) }}" min="1" max="5" placeholder="5" class="w-full rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-slate-100">
                    </div>
                </div>
                <p class="text-[11px] text-slate-500">Kosongkan keduanya untuk randomize 1–5 (default).</p>

                <div>
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">Prediksi kemenangan — kelipatan modal (50–300×)</label>
                    <input type="number" name="maxwin_multiplier" value="{{ old('maxwin_multiplier', $game->maxwin_multiplier) }}" min="50" max="300" placeholder="150" class="w-full max-w-xs rounded-lg border border-white/10 bg-black/30 px-3 py-2 text-slate-100">
                    <p class="mt-1 text-[11px] text-slate-500">Contoh: 150 → modal 50.000 diprediksi menang 7.500.000. Kelipatan tinggi = difficulty tinggi.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-black">Simpan</button>
            <a href="{{ route('admin.game.index') }}" class="rounded-xl border border-white/15 px-6 py-2.5 text-slate-300">Batal</a>
        </div>
    </form>

    @if($isEdit)
        <div class="mt-8 border-t border-white/10 pt-4">
            <form method="post" action="{{ route('admin.game.destroy', $game) }}" onsubmit="return confirm('Hapus game ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-rose-400 hover:underline">Hapus game</button>
            </form>
        </div>
    @endif
</div>
@endsection
