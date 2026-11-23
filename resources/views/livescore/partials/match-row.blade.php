{{-- $f (Fixture), $showLeague (bool) --}}
@php
    $live = $f->isLive();
    $done = $f->isFinished();
    $homeWin = $f->hasScore() && $f->goals_home > $f->goals_away;
    $awayWin = $f->hasScore() && $f->goals_away > $f->goals_home;
@endphp
<li>
    <a href="{{ route('pertandingan.show', $f) }}"
       class="match-row group grid grid-cols-[52px_1fr_auto] items-center gap-3 px-4 py-3 sm:grid-cols-[64px_1fr_auto] sm:px-5 {{ $live ? 'is-live' : '' }}"
       aria-label="{{ $f->title() }}{{ $f->hasScore() ? ', skor '.$f->goals_home.'-'.$f->goals_away : ', kickoff '.$f->localKickoff()->format('H:i') }}">
        <span class="flex flex-col items-start">
            <span class="text-xs font-semibold tabular-nums {{ $live ? 'text-[color:var(--live)]' : ($done ? 'text-[color:var(--muted)]' : 'text-[color:var(--ink)]') }}">
                @if($live)<span class="live-dot mr-1 inline-block h-1.5 w-1.5 rounded-full bg-[color:var(--live)] align-middle" aria-hidden="true"></span>@endif{{ $f->statusLabel() }}
            </span>
            @if(!empty($showLeague))
                <span class="mt-0.5 max-w-[60px] truncate text-[9px] uppercase tracking-wider text-[color:var(--muted)] sm:max-w-none">{{ $f->league->displayName() }}</span>
            @elseif($done || $live)
                <span class="mt-0.5 text-[10px] tabular-nums text-[color:var(--muted)]">{{ $f->localKickoff()->format('H:i') }}</span>
            @endif
        </span>
        <span class="min-w-0 space-y-1.5">
            <span class="flex items-center gap-2.5">
                @include('livescore.partials.team-logo', ['team' => $f->home, 'size' => 20])
                <span class="truncate text-sm {{ $homeWin ? 'font-semibold text-[color:var(--ink)]' : 'text-[color:var(--ink)]/85' }}">{{ $f->home->name }}</span>
            </span>
            <span class="flex items-center gap-2.5">
                @include('livescore.partials.team-logo', ['team' => $f->away, 'size' => 20])
                <span class="truncate text-sm {{ $awayWin ? 'font-semibold text-[color:var(--ink)]' : 'text-[color:var(--ink)]/85' }}">{{ $f->away->name }}</span>
            </span>
        </span>
        <span class="flex items-center gap-3">
            @if($f->hasScore())
                <span class="space-y-1.5 text-right text-sm font-semibold tabular-nums {{ $live ? 'text-[color:var(--live)]' : 'text-[color:var(--ink)]' }}">
                    <span class="block {{ $done && ! $homeWin ? 'opacity-60' : '' }}">{{ $f->goals_home }}</span>
                    <span class="block {{ $done && ! $awayWin ? 'opacity-60' : '' }}">{{ $f->goals_away }}</span>
                </span>
            @else
                <span class="text-xs text-[color:var(--muted)]">vs</span>
            @endif
            <i class="fas fa-chevron-right text-[10px] text-[color:var(--muted)] opacity-0 transition group-hover:opacity-100" aria-hidden="true"></i>
        </span>
    </a>
</li>
