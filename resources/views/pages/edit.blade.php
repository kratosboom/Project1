@extends('layouts.admin')

@section('title', 'Edit — '.$page->title)

@section('content')
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-white">Edit halaman</h1>
        <p class="mt-1 font-mono text-sm text-cyan-300/90">{{ $page->slug }}</p>
    </div>
    @if($page->is_published)
        <a href="{{ route('halaman.show', $page) }}" class="text-sm text-primary hover:underline" target="_blank" rel="noopener">Pratinjau publik</a>
    @endif
</div>
<div class="glass max-w-3xl rounded-2xl p-6 sm:p-8">
    @include('pages.partials.form', ['page' => $page])
</div>
@endsection
