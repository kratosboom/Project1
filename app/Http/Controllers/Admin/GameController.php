<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameProvider;
use App\Services\ClickTrackerService;
use App\Services\GameHtmlScraperService;
use App\Services\GameRandomizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $query = Game::query()->with('provider');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('provider', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        $games = $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $gameProviders = GameProvider::query()->orderBy('sort_order')->get();

        return view('admin.game.index', compact('games', 'gameProviders', 'search'));
    }

    public function create(): View
    {
        $game = new Game([
            'rtp' => 96.5,
            'is_best' => false,
            'jam_gacor' => '12.00 - 15.00',
            'pola' => ['turbo' => '20X', 'auto' => '50X', 'manual' => '100X'],
            'sort_order' => 0,
        ]);
        $gameProviders = GameProvider::query()->orderBy('sort_order')->get();

        return view('admin.game.form', compact('game', 'gameProviders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Game::query()->create($data);

        return redirect()->route('admin.game.index')->with('ok', 'Game ditambah.');
    }

    public function edit(Game $game): View
    {
        $gameProviders = GameProvider::query()->orderBy('sort_order')->get();

        return view('admin.game.form', ['game' => $game, 'gameProviders' => $gameProviders]);
    }

    public function update(Request $request, Game $game): RedirectResponse
    {
        $game->update($this->validateData($request));

        return redirect()->route('admin.game.index')->with('ok', 'Game diperbarui.');
    }

    public function destroy(Game $game): RedirectResponse
    {
        $game->delete();

        return redirect()->route('admin.game.index')->with('ok', 'Game dihapus.');
    }

    public function resetClicks(ClickTrackerService $tracker): RedirectResponse
    {
        $count = $tracker->resetAll();

        return back()->with('ok', "Reset click count untuk {$count} game. Status Hot akan dihitung ulang dari klik user berikutnya.");
    }

    public function recomputeHot(ClickTrackerService $tracker): RedirectResponse
    {
        $tracker->recomputeHotFlags();

        return back()->with('ok', 'Hot flags disinkronkan dari click_count.');
    }

    public function randomizeAll(GameRandomizerService $randomizer): RedirectResponse
    {
        $count = $randomizer->randomizeAll();

        return redirect()->route('admin.game.index')->with('ok', "Acak selesai untuk {$count} game.");
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'import_json' => ['required', 'string', 'min:2'],
        ]);
        try {
            $decoded = json_decode($request->string('import_json'), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return back()->withErrors(['import_json' => 'JSON tidak valid.'])->onlyInput('import_json');
        }
        if (! is_array($decoded) || ! isset($decoded['games']) || ! is_array($decoded['games'])) {
            return back()->withErrors(['import_json' => 'Format JSON: {"games": [...]}  — lihat bantuan di halaman.'])->onlyInput('import_json');
        }

        $bySlug = GameProvider::query()->pluck('id', 'slug');
        $byName = GameProvider::query()
            ->get()
            ->keyBy(fn (GameProvider $p) => strtoupper($p->name))
            ->map(fn (GameProvider $p) => $p->id);

        $n = 0;
        DB::transaction(function () use ($decoded, $bySlug, $byName, &$n) {
            foreach ($decoded['games'] as $row) {
                if (! is_array($row) || empty($row['name'])) {
                    continue;
                }
                $provKey = $row['provider'] ?? $row['game_provider'] ?? 'PRAGMATIC';
                $pid = is_numeric($provKey)
                    ? (int) $provKey
                    : ($bySlug->get(strtolower($provKey)) ?? $byName->get(strtoupper($provKey)));
                if (! $pid) {
                    $name = (string) $provKey;
                    $base = \Illuminate\Support\Str::slug($name) ?: 'provider';
                    $slug = $base;
                    $i = 0;
                    while (GameProvider::query()->where('slug', $slug)->exists()) {
                        $i++;
                        $slug = $base.'-'.$i;
                    }
                    $p = GameProvider::query()->create([
                        'name' => $name,
                        'slug' => $slug,
                        'icon_class' => 'fa-crown',
                        'sort_order' => 100,
                    ]);
                    $pid = $p->id;
                }
                $pola = $row['pola'] ?? $row['pola_gacor'] ?? ['turbo' => '20X', 'auto' => '50X', 'manual' => '100X'];
                if (is_string($pola)) {
                    $pola = json_decode($pola, true) ?: ['turbo' => '20X', 'auto' => '50X', 'manual' => '100X'];
                }
                Game::query()->create([
                    'game_provider_id' => $pid,
                    'name' => $row['name'],
                    'image_url' => $this->resolveImportImageUrl($row),
                    'rtp' => (float) ($row['rtp'] ?? 96.5),
                    'is_hot' => (bool) ($row['hot'] ?? $row['is_hot'] ?? false),
                    'is_best' => (bool) ($row['best'] ?? $row['is_best'] ?? false),
                    'jam_gacor' => (string) ($row['jam'] ?? $row['jam_gacor'] ?? '12.00 - 15.00'),
                    'pola' => is_array($pola) ? $pola : ['turbo' => '20X', 'auto' => '50X', 'manual' => '100X'],
                    'modal_data' => isset($row['modal']) && is_array($row['modal']) ? $row['modal'] : null,
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                ]);
                $n++;
            }
        });

        return redirect()->route('admin.game.index')->with('ok', "Impor selesai: {$n} game.");
    }

    public function previewHtml(Request $request, GameHtmlScraperService $scraper): View|RedirectResponse
    {
        $data = $this->validateScrapeInput($request);

        $html = $this->resolveHtml($data);

        if ($html === null) {
            return back()
                ->withErrors(['html' => 'Isi URL sumber atau paste HTML. Jika URL mengembalikan 403/Cloudflare, buka di browser lalu paste view-source.'])
                ->withInput();
        }

        $provider = $this->resolveProviderForPreview($data);
        $filter = ! empty($data['filter_provider']) ? $data['filter_provider'] : null;
        $cards = $scraper->parse($html, $filter);

        if ($cards === []) {
            return back()
                ->withErrors(['html' => 'Tidak menemukan elemen .game-card atau .game-box pada HTML. Pastikan struktur HTML sesuai.'])
                ->withInput();
        }

        $games = Game::query()->with('provider')->orderBy('sort_order')->orderBy('id')->get();
        $gameProviders = GameProvider::query()->orderBy('sort_order')->get();

        return view('admin.game.index', [
            'games' => $games,
            'gameProviders' => $gameProviders,
            'previewResult' => [
                'cards' => $cards,
                'provider' => $provider,
                'filter' => $filter,
                'mode' => $data['mode'],
                'html' => $html,
                'source_url' => $data['source_url'] ?? null,
                'provider_name' => trim((string) ($data['provider_name'] ?? '')),
            ],
        ]);
    }

    public function importHtml(Request $request, GameHtmlScraperService $scraper): RedirectResponse
    {
        $data = $this->validateScrapeInput($request);

        $html = $this->resolveHtml($data);

        if ($html === null) {
            return back()
                ->withErrors(['html' => 'Isi URL sumber atau paste HTML. Jika URL mengembalikan 403/Cloudflare, buka di browser lalu paste view-source.'])
                ->onlyInput('filter_provider', 'mode', 'source_url', 'provider_name', 'html');
        }

        $provider = $this->resolveOrCreateProviderForScrape($data);
        $filter = ! empty($data['filter_provider']) ? $data['filter_provider'] : null;

        $cards = $scraper->parse($html, $filter);

        if ($cards === []) {
            return back()
                ->withErrors(['html' => 'Tidak menemukan elemen .game-card atau .game-box pada HTML.'])
                ->onlyInput('filter_provider', 'mode', 'source_url', 'provider_name', 'html');
        }

        $result = $scraper->upsert($cards, $provider, $data['mode'] === 'fresh');

        $msg = sprintf(
            'Impor HTML selesai: %d baru, %d diperbarui%s (provider: %s).',
            $result['created'],
            $result['updated'],
            $result['deleted'] > 0 ? ", {$result['deleted']} dihapus" : '',
            $provider->name,
        );

        return redirect()->route('admin.game.index')->with('ok', $msg);
    }

    /**
     * @return array{html:?string,source_url:?string,provider_name:?string,filter_provider:?string,mode:string}
     */
    private function validateScrapeInput(Request $request): array
    {
        $data = $request->validate([
            'html' => ['nullable', 'string'],
            'source_url' => ['nullable', 'url', 'max:2000'],
            'provider_name' => ['nullable', 'string', 'max:120'],
            'filter_provider' => ['nullable', 'string', 'max:64'],
            'mode' => ['required', 'in:upsert,fresh'],
        ]);

        $data['source_url'] = isset($data['source_url']) ? trim((string) $data['source_url']) : null;
        if ($data['source_url'] === '') {
            $data['source_url'] = null;
        }

        return $data;
    }

    /**
     * Kategori (provider) dibuat otomatis jika belum ada: prioritas nama manual,
     * lalu segmen terakhir URL, atau hostname. Wajib salah satu sumber (nama atau URL) jika scrape dari HTML saja.
     */
    private function resolveOrCreateProviderForScrape(array $data): GameProvider
    {
        $derived = $this->deriveProviderNameAndSlug($data);
        $name = $derived['name'];
        $slug = $derived['slug'];

        $existing = GameProvider::query()->where('slug', $slug)->first();

        if ($existing) {
            return $existing;
        }

        $nextOrder = (int) (GameProvider::query()->max('sort_order') ?? 0) + 1;

        return GameProvider::query()->create([
            'name' => $name,
            'slug' => $slug,
            'sort_order' => $nextOrder,
        ]);
    }

    /**
     * Pratinjau: gunakan record DB jika ada, atau model sementara (belum disimpan) supaya kategori tidak tercipta hanya karena klik pratinjau.
     */
    private function resolveProviderForPreview(array $data): GameProvider
    {
        $derived = $this->deriveProviderNameAndSlug($data);
        $name = $derived['name'];
        $slug = $derived['slug'];

        $existing = GameProvider::query()->where('slug', $slug)->first();

        if ($existing) {
            return $existing;
        }

        return new GameProvider([
            'name' => $name,
            'slug' => $slug,
            'sort_order' => 0,
        ]);
    }

    /**
     * @return array{name: string, slug: string}
     */
    private function deriveProviderNameAndSlug(array $data): array
    {
        $manual = trim((string) ($data['provider_name'] ?? ''));
        $sourceUrl = $data['source_url'] ?? null;

        if ($manual !== '') {
            $name = $manual;
            $slug = Str::slug($name);
        } elseif ($sourceUrl !== null && $sourceUrl !== '') {
            $slug = $this->slugFromSourceUrl($sourceUrl);
            $name = $this->humanNameFromSlug($slug);
        } else {
            throw ValidationException::withMessages([
                'provider_name' => 'Isi nama provider / kategori baru, atau isi URL sumber agar dibuat otomatis dari alamat tersebut.',
            ]);
        }

        if ($slug === '') {
            $slug = 'provider-'.Str::lower(Str::random(8));
        }

        if ($name === '') {
            $name = Str::title(str_replace(['-', '_'], ' ', $slug));
        }

        return ['name' => $name, 'slug' => $slug];
    }

    private function slugFromSourceUrl(string $url): string
    {
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        if ($segments !== []) {
            return Str::slug(end($segments));
        }

        $host = (string) (parse_url($url, PHP_URL_HOST) ?? '');

        if ($host !== '') {
            $host = preg_replace('/^www\./i', '', $host) ?? $host;

            return Str::slug(explode('.', $host)[0] ?? $host) ?: 'imported';
        }

        return 'imported';
    }

    private function humanNameFromSlug(string $slug): string
    {
        $s = str_replace(['-', '_'], ' ', $slug);

        return Str::title($s);
    }

    /**
     * Prefer pasted HTML; otherwise try to fetch the source URL with a browser UA.
     *
     * @param  array<string,mixed>  $data
     */
    private function resolveHtml(array $data): ?string
    {
        $pasted = trim((string) ($data['html'] ?? ''));

        if (Str::length($pasted) >= 100) {
            return $pasted;
        }

        $url = trim((string) ($data['source_url'] ?? ''));

        if ($url === '') {
            return null;
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.9,id;q=0.8',
                ])
                ->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $body = (string) $response->body();

        if (Str::length($body) < 100) {
            return null;
        }

        return $body;
    }

    private function validateData(Request $request): array
    {
        $v = $request->validate([
            'game_provider_id' => ['required', 'exists:game_providers,id'],
            'name' => ['required', 'string', 'max:200'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'rtp' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_best' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'jam_gacor' => ['nullable', 'string', 'max:64'],
            'pola_turbo' => ['nullable', 'string', 'max:32'],
            'pola_auto' => ['nullable', 'string', 'max:32'],
            'pola_manual' => ['nullable', 'string', 'max:32'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'maxwin_footer_text' => ['nullable', 'string', 'max:280'],
            'maxwin_difficulty_min' => ['nullable', 'integer', 'min:1', 'max:5'],
            'maxwin_difficulty_max' => ['nullable', 'integer', 'min:1', 'max:5'],
            'maxwin_multiplier' => ['nullable', 'integer', 'min:50', 'max:300'],
        ]);
        // is_hot is derived from click_count via ClickTrackerService — never set from form.
        $v['is_best'] = $request->boolean('is_best');
        $v['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        $v['sort_order'] = (int) ($v['sort_order'] ?? 0);
        $v['pola'] = [
            'turbo' => $v['pola_turbo'] ?? '20X',
            'auto' => $v['pola_auto'] ?? '50X',
            'manual' => $v['pola_manual'] ?? '100X',
        ];
        unset($v['pola_turbo'], $v['pola_auto'], $v['pola_manual']);

        return $v;
    }

    /**
     * Satu sumber URL gambar dari JSON impor. Prioritas: source (khusus input Anda) lalu img/image/dll.
     *
     * @param  array<string, mixed>  $row
     */
    private function resolveImportImageUrl(array $row): ?string
    {
        $keys = [
            'source',
            'img',
            'image',
            'image_url',
            'src',
            'thumbnail',
            'thumb',
            'cover',
            'cover_url',
        ];
        foreach ($keys as $k) {
            if (! array_key_exists($k, $row) || $row[$k] === null) {
                continue;
            }
            if (is_string($row[$k])) {
                $u = trim($row[$k]);

                return $u !== '' ? $u : null;
            }
        }

        return null;
    }
}
