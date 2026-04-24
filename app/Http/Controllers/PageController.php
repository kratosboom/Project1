<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::query()->orderByDesc('updated_at')->get();

        return view('pages.index', compact('pages'));
    }

    public function show(Page $page): View
    {
        if (! $page->is_published) {
            abort(404);
        }

        return view('pages.show', compact('page'));
    }

    public function create(): View
    {
        return view('pages.create', ['page' => new Page(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $base = ! empty($data['slug']) ? $data['slug'] : $data['title'];
        $data['slug'] = Page::uniqueSlug($base);
        Page::query()->create($data);

        return redirect()->route('admin.halaman.index')->with('ok', 'Halaman berhasil dibuat.');
    }

    public function edit(Page $page): View
    {
        return view('pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validated($request);
        $base = ! empty($data['slug']) ? $data['slug'] : $data['title'];
        $data['slug'] = Page::uniqueSlug($base, $page->id);
        $page->update($data);

        return redirect()->route('admin.halaman.index')->with('ok', 'Halaman diperbarui.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.halaman.index')->with('ok', 'Halaman dihapus.');
    }

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ], [], [
            'title' => 'judul',
        ]);

        $v['is_published'] = $request->boolean('is_published');

        return $v;
    }
}
