@php
    $isEdit = $page->exists;
@endphp
<form method="post" action="{{ $isEdit ? route('admin.halaman.update', $page) : route('admin.halaman.store') }}" class="space-y-6">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    <div>
        <label for="title" class="mb-1 block text-sm font-medium text-slate-300">Judul</label>
        <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required
            class="w-full rounded-xl border border-white/10 bg-ink-900/60 px-4 py-2.5 text-white placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/50">
        @error('title')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="slug" class="mb-1 block text-sm font-medium text-slate-300">Slug <span class="text-slate-500">(opsional, otomatis dari judul)</span></label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}"
            class="w-full rounded-xl border border-white/10 bg-ink-900/60 px-4 py-2.5 font-mono text-sm text-cyan-200 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/50"
            placeholder="contoh: tentang-kami">
        @error('slug')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="excerpt" class="mb-1 block text-sm font-medium text-slate-300">Ringkasan</label>
        <input type="text" name="excerpt" id="excerpt" value="{{ old('excerpt', $page->excerpt) }}"
            class="w-full rounded-xl border border-white/10 bg-ink-900/60 px-4 py-2.5 text-white placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/50"
            placeholder="Satu baris untuk kartu & SEO">
        @error('excerpt')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="body" class="mb-1 block text-sm font-medium text-slate-300">Isi halaman</label>
        <textarea name="body" id="body" rows="12" required
            class="w-full rounded-xl border border-white/10 bg-ink-900/60 px-4 py-3 text-white placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/50"
            placeholder="Konten panjang, bisa beberapa paragraf.">{{ old('body', $page->body) }}</textarea>
        @error('body')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
    </div>
    <div class="flex items-center gap-3">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" id="is_published" value="1" @checked(old('is_published', $page->is_published))
            class="h-4 w-4 rounded border-slate-500 bg-ink-900 text-primary focus:ring-primary/50">
        <label for="is_published" class="text-sm text-slate-300">Publikasikan (dapat diakses di URL publik)</label>
    </div>
    <div class="flex flex-wrap gap-3">
        <button type="submit" class="rounded-full bg-primary px-6 py-2.5 font-bold text-black shadow-lg shadow-primary/25 transition hover:brightness-110">
            {{ $isEdit ? 'Simpan perubahan' : 'Simpan halaman' }}
        </button>
        <a href="{{ route('admin.halaman.index') }}" class="rounded-xl border border-white/15 px-6 py-2.5 text-slate-300 hover:bg-white/5">Batal</a>
    </div>
</form>
