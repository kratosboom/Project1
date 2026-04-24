@extends('layouts.admin')

@section('title', 'Halaman baru — '.config('app.name'))

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold tracking-tight text-white">Buat halaman baru</h1>
    <p class="mt-1 text-slate-400">Isi form berikut. Slug akan dihasilkan unik otomatis bila dikosongkan.</p>
</div>
<div class="glass max-w-3xl rounded-2xl p-6 sm:p-8">
    @include('pages.partials.form', ['page' => $page])
</div>
@endsection
