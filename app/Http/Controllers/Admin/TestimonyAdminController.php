<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimony;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonyAdminController extends Controller
{
    public function index(): View
    {
        $testimonies = Testimony::query()->orderBy('sort_order')->orderByDesc('id')->get();

        return view('admin.testimoni.index', compact('testimonies'));
    }

    public function create(): View
    {
        return view('admin.testimoni.form', [
            'testimony' => new Testimony([
                'rating' => 5,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Testimony::query()->create($data);

        return redirect()->route('admin.testimoni.index')->with('ok', 'Testimoni disimpan.');
    }

    public function edit(Testimony $testimony): View
    {
        return view('admin.testimoni.form', compact('testimony'));
    }

    public function update(Request $request, Testimony $testimony): RedirectResponse
    {
        $testimony->update($this->validated($request));

        return redirect()->route('admin.testimoni.index')->with('ok', 'Testimoni diperbarui.');
    }

    public function destroy(Testimony $testimony): RedirectResponse
    {
        $testimony->delete();

        return redirect()->route('admin.testimoni.index')->with('ok', 'Testimoni dihapus.');
    }

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'author_name' => ['required', 'string', 'max:120'],
            'author_role' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:10000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'proof_images_text' => ['nullable', 'string', 'max:20000'],
        ]);
        $v['sort_order'] = (int) ($v['sort_order'] ?? 0);
        $v['proof_images'] = $this->parseProofImages($request->string('proof_images_text'));
        unset($v['proof_images_text']);

        return $v;
    }

    private function parseProofImages(string $raw): ?array
    {
        $t = trim($raw);
        if ($t === '') {
            return null;
        }
        if (str_starts_with($t, '[')) {
            $dec = json_decode($t, true);
            if (is_array($dec)) {
                return array_values(array_filter(array_map('strval', $dec)));
            }
        }
        $lines = array_filter(array_map('trim', explode("\n", $t)));

        return $lines === [] ? null : array_values($lines);
    }
}
