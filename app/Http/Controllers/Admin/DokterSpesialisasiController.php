<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokterSpesialisasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DokterSpesialisasiController extends Controller
{
    public function index(): View
    {
        $spesialisasis = DokterSpesialisasi::query()
            ->withCount('dokters')
            ->ordered()
            ->paginate(20);

        return view('admin.dokter-spesialisasi.index', compact('spesialisasis'));
    }

    public function create(): View
    {
        return view('admin.dokter-spesialisasi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['name' => 'nama spesialisasi']);

        DokterSpesialisasi::create([
            'name' => $validated['name'],
            'slug' => DokterSpesialisasi::uniqueSlug($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.dokter-spesialisasi.index')
            ->with('success', 'Spesialisasi berhasil ditambahkan.');
    }

    public function edit(DokterSpesialisasi $dokterSpesialisasi): View
    {
        return view('admin.dokter-spesialisasi.edit', ['spesialisasi' => $dokterSpesialisasi]);
    }

    public function update(Request $request, DokterSpesialisasi $dokterSpesialisasi): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['name' => 'nama spesialisasi']);

        $slug = $dokterSpesialisasi->slug;
        if ($dokterSpesialisasi->name !== $validated['name']) {
            $slug = DokterSpesialisasi::uniqueSlug($validated['name'], $dokterSpesialisasi->id);
        }

        $dokterSpesialisasi->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        $dokterSpesialisasi->dokters()->update(['specialty' => $validated['name']]);

        return redirect()
            ->route('admin.dokter-spesialisasi.index')
            ->with('success', 'Spesialisasi berhasil diperbarui.');
    }

    public function destroy(DokterSpesialisasi $dokterSpesialisasi): RedirectResponse
    {
        if ($dokterSpesialisasi->dokters()->exists()) {
            return back()->with('error', 'Spesialisasi masih dipakai dokter. Pindahkan dokter dulu.');
        }

        $dokterSpesialisasi->delete();

        return redirect()
            ->route('admin.dokter-spesialisasi.index')
            ->with('success', 'Spesialisasi berhasil dihapus.');
    }
}
