<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LayananLinkController extends Controller
{
    public function index(): View
    {
        $links = LayananLink::query()
            ->ordered()
            ->paginate(20);

        return view('admin.link-layanan.index', compact('links'));
    }

    public function create(): View
    {
        return view('admin.link-layanan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        LayananLink::create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'description' => $validated['description'] ?? null,
            'logo_path' => $request->hasFile('logo') ? $this->storeLogo($request) : null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => ((int) LayananLink::query()->max('sort_order')) + 1,
        ]);

        return redirect()
            ->route('admin.link-layanan.index')
            ->with('success', 'Link layanan berhasil ditambahkan.');
    }

    public function edit(LayananLink $linkLayanan): View
    {
        return view('admin.link-layanan.edit', ['link' => $linkLayanan]);
    }

    public function update(Request $request, LayananLink $linkLayanan): RedirectResponse
    {
        $validated = $this->validated($request, updating: true);

        $data = [
            'title' => $validated['title'],
            'url' => $validated['url'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->boolean('remove_logo') && $linkLayanan->logo_path) {
            Storage::disk('public')->delete($linkLayanan->logo_path);
            $data['logo_path'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($linkLayanan->logo_path) {
                Storage::disk('public')->delete($linkLayanan->logo_path);
            }
            $data['logo_path'] = $this->storeLogo($request);
        }

        $linkLayanan->update($data);

        return redirect()
            ->route('admin.link-layanan.index')
            ->with('success', 'Link layanan berhasil diperbarui.');
    }

    public function destroy(LayananLink $linkLayanan): RedirectResponse
    {
        if ($linkLayanan->logo_path) {
            Storage::disk('public')->delete($linkLayanan->logo_path);
        }

        $linkLayanan->delete();

        return redirect()
            ->route('admin.link-layanan.index')
            ->with('success', 'Link layanan berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:255'],
            'logo' => [$updating ? 'nullable' : 'nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'remove_logo' => ['nullable', 'boolean'],
        ], [], [
            'title' => 'nama layanan',
            'url' => 'tautan',
            'description' => 'deskripsi singkat',
            'logo' => 'logo',
        ]);
    }

    private function storeLogo(Request $request): string
    {
        return $request->file('logo')->store('layanan-links', 'public');
    }
}
