<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameProvider;
use App\Services\ScrapeLogoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GameProviderController extends Controller
{
    public function index(): View
    {
        $providers = GameProvider::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.provider.index', compact('providers'));
    }

    public function create(): View
    {
        return view('admin.provider.form', [
            'gameProvider' => new GameProvider([
                'icon_class' => 'fa-crown',
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        GameProvider::query()->create($data);

        return redirect()->route('admin.provider.index')->with('ok', 'Provider ditambah.');
    }

    public function edit(GameProvider $game_provider): View
    {
        return view('admin.provider.form', ['gameProvider' => $game_provider]);
    }

    public function update(Request $request, GameProvider $game_provider): RedirectResponse
    {
        $data = $this->validateData($request);
        if (Str::slug($data['name']) !== $game_provider->slug) {
            $data['slug'] = $this->uniqueSlug($data['name'], $game_provider->id);
        }
        $game_provider->update($data);

        return redirect()->route('admin.provider.index')->with('ok', 'Provider diperbarui.');
    }

    public function destroy(GameProvider $game_provider): RedirectResponse
    {
        $game_provider->delete();

        return redirect()->route('admin.provider.index')->with('ok', 'Provider dihapus (game terkait ikut terhapus).');
    }

    public function fetchLogo(Request $request, GameProvider $game_provider, ScrapeLogoService $scraper): RedirectResponse
    {
        $request->validate(['website_url' => ['required', 'url', 'max:2000']]);
        $logo = $scraper->extractLogoUrl($request->string('website_url'));
        if (! $logo) {
            return back()->withErrors(['website_url' => 'Tidak menemukan og:image / icon dari URL tersebut.'])->onlyInput('website_url');
        }
        $game_provider->update(['logo_url' => $logo, 'website_url' => $request->string('website_url')]);

        return back()->with('ok', 'Logo diambil otomatis.');
    }

    private function validateData(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'logo_url' => ['nullable', 'string', 'max:2000'],
            'website_url' => ['nullable', 'string', 'max:2000'],
            'icon_class' => ['nullable', 'string', 'max:64'],
            'is_hot_games' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
        $v['is_hot_games'] = $request->boolean('is_hot_games');
        $v['icon_class'] = $v['icon_class'] ?: 'fa-crown';
        $v['sort_order'] = (int) ($v['sort_order'] ?? 0);

        return $v;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'provider';
        $slug = $base;
        $i = 0;
        while (GameProvider::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $i++;
            $slug = $base.'-'.$i;
        }

        return $slug;
    }
}
