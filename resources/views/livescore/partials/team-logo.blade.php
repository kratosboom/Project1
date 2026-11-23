{{-- $team (Team), $size (px) --}}
@php
    $size = $size ?? 28;
    $initials = collect(preg_split('/\s+/', trim($team->name)))->filter()->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
@endphp
@if($team->logo)
    <img src="{{ $team->logo }}" alt="Logo {{ $team->name }}" width="{{ $size }}" height="{{ $size }}" loading="lazy" decoding="async"
         class="shrink-0 object-contain" style="width: {{ $size }}px; height: {{ $size }}px;"
         onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'team-initials',textContent:'{{ $initials }}',style:'width:{{ $size }}px;height:{{ $size }}px;font-size:{{ max(9, intdiv($size, 3)) }}px'}))">
@else
    <span class="team-initials" style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ max(9, intdiv($size, 3)) }}px;" aria-hidden="true">{{ $initials }}</span>
@endif
