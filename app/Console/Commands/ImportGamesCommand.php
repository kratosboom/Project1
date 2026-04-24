<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\GameProvider;
use App\Services\GameHtmlScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportGamesCommand extends Command
{
    protected $signature = 'games:import
        {path : Path to the saved HTML file (absolute, or relative to project base)}
        {--provider=pragmatic : Slug of the GameProvider to attach games to}
        {--filter-provider= : Only import cards whose data-provider matches this value (e.g. PRAGMATIC)}
        {--fresh : Delete all existing games for the provider before import}
        {--dry-run : Parse and preview without writing to the database}';

    protected $description = 'Scrape a saved slot listing HTML page and upsert games into the database.';

    public function handle(GameHtmlScraperService $scraper): int
    {
        $rawPath = (string) $this->argument('path');
        $absolute = $this->resolvePath($rawPath);

        if ($absolute === null || ! is_file($absolute)) {
            $this->error("HTML file not found: {$rawPath}");

            return self::FAILURE;
        }

        $providerSlug = (string) $this->option('provider');
        $provider = GameProvider::query()->where('slug', $providerSlug)->first();

        if (! $provider) {
            $this->error("GameProvider with slug '{$providerSlug}' does not exist. Seed it first or pass --provider=<existing-slug>.");

            return self::FAILURE;
        }

        $html = (string) file_get_contents($absolute);

        if ($html === '') {
            $this->error('HTML file is empty.');

            return self::FAILURE;
        }

        $filter = (string) $this->option('filter-provider');
        $cards = $scraper->parse($html, $filter !== '' ? $filter : null);

        if ($cards === []) {
            $this->warn('No .game-card elements were found in the HTML.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Parsed %d game card(s) from %s', count($cards), basename($absolute)));

        if ($this->option('dry-run')) {
            $this->table(
                ['Name', 'Provider', 'RTP', 'Hot', 'Best', 'Jam Gacor', 'Image'],
                array_map(
                    fn (array $g): array => [
                        $g['name'],
                        $g['provider_label'],
                        $g['rtp'],
                        $g['is_hot'] ? 'yes' : 'no',
                        $g['is_best'] ? 'yes' : 'no',
                        $g['jam_gacor'],
                        Str::limit($g['image_url'] ?? '', 50),
                    ],
                    $cards,
                ),
            );
            $this->comment('Dry run: nothing was written.');

            return self::SUCCESS;
        }

        $result = $scraper->upsert($cards, $provider, (bool) $this->option('fresh'));

        if ($result['deleted'] > 0) {
            $this->line("Removed {$result['deleted']} existing game(s) for provider {$provider->name}.");
        }

        $this->info(sprintf('Done. Created: %d, Updated: %d.', $result['created'], $result['updated']));

        return self::SUCCESS;
    }

    private function resolvePath(string $path): ?string
    {
        $normalized = str_replace('\\', '/', $path);

        if (is_file($normalized)) {
            return realpath($normalized) ?: $normalized;
        }

        $candidate = base_path($normalized);

        if (is_file($candidate)) {
            return realpath($candidate) ?: $candidate;
        }

        $candidate = storage_path('scrape/'.ltrim($normalized, '/'));

        if (is_file($candidate)) {
            return realpath($candidate) ?: $candidate;
        }

        return null;
    }
}
