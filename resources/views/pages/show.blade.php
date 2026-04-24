@extends('layouts.app')

@section('title', $page->title.' — '.config('app.name'))

@section('content')
<article class="mx-auto max-w-3xl">
    <p class="mb-2 text-xs font-medium uppercase tracking-widest text-primary/90">Halaman</p>
    <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{{ $page->title }}</h1>
    @if($page->excerpt)
        <p class="mt-4 text-lg text-slate-400">{{ $page->excerpt }}</p>
    @endif
    <div class="mt-8 text-base leading-relaxed text-slate-300">
        {!! nl2br(e($page->body)) !!}
    </div>
    <p class="mt-10 text-xs text-slate-500">Terakhir diperbarui {{ $page->updated_at->translatedFormat('d F Y, H:i') }}</p>
</article>
@endsection
