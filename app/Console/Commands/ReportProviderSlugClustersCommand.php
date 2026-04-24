<?php

namespace App\Console\Commands;

use App\Models\GameProvider;
use App\Support\GameProviderBrandMatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ReportProviderSlugClustersCommand extends Command
{
    protected $signature = 'rtp:provider-clusters {--json : Output as JSON}';

    protected $description = 'Kelompokkan provider by slug ternormalisasi (deteksi duplikat / tab ganda)';

    public function handle(): int
    {
        $all = GameProvider::query()
            ->withCount('games')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        /** @var array<string, Collection<int, GameProvider>> $buckets */
        $buckets = [];
        foreach ($all as $p) {
            $key = GameProviderBrandMatcher::groupKey($p);
            $buckets[$key] ??= new Collection;
            $buckets[$key]->push($p);
        }

        $rows = [];
        foreach ($buckets as $key => $group) {
            if ($group->count() < 2) {
                continue;
            }
            $winner = GameProviderBrandMatcher::chooseCanonical($group);
            $rows[] = [
                'cluster' => $key,
                'winner_slug' => $winner?->slug,
                'winner_games' => $winner?->games_count,
                'rows' => $group->map(fn (GameProvider $p) => [
                    'id' => $p->id,
                    'slug' => $p->slug,
                    'name' => $p->name,
                    'games' => $p->games_count,
                    'is_hot_games' => $p->is_hot_games,
                ])->values()->all(),
            ];
        }

        if ($this->option('json')) {
            $this->line(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        if ($rows === []) {
            $this->info('Tidak ada cluster duplikat (lebih dari satu provider per slug ternormalisasi).');

            return self::SUCCESS;
        }

        $this->warn('Ditemukan '.count($rows).' cluster (kemungkinan tab ganda / URL kosong):');
        foreach ($rows as $r) {
            $this->newLine();
            $this->line('<fg=cyan>Cluster:</> '.$r['cluster'].' <fg=gray>| kanonik: '.$r['winner_slug'].' ('.$r['winner_games'].' game)</>');
            foreach ($r['rows'] as $line) {
                $mark = ($line['slug'] === $r['winner_slug']) ? ' ← dipakai' : '';
                $this->line(sprintf(
                    '  id=%d slug=%s games=%d name=%s%s',
                    $line['id'],
                    $line['slug'],
                    $line['games'],
                    $line['name'],
                    $mark,
                ));
            }
        }

        $this->newLine();
        $this->comment('Tip: di beranda, tab & URL sudah digabung otomatis ke provider dengan game terbanyak. Hapus baris provider kosong di admin bila ingin DB rapi.');

        return self::SUCCESS;
    }
}
