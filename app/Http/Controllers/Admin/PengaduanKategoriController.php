<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaduanKategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PengaduanKategoriController extends Controller
{
    public function index(): View
    {
        $categories = PengaduanKategori::query()
            ->withCount('pengaduans')
            ->ordered()
            ->paginate(15);

        return view('admin.pengaduan-kategori.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.pengaduan-kategori.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nama kategori',
            'sort_order' => 'urutan',
        ]);

        PengaduanKategori::create([
            'name' => $validated['name'],
            'slug' => PengaduanKategori::uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        Cache::forget('public_pengaduan_kategoris');

        return redirect()
            ->route('admin.pengaduan-kategori.index')
            ->with('success', 'Kategori pengaduan berhasil ditambahkan.');
    }

    public function edit(PengaduanKategori $pengaduanKategori): View
    {
        return view('admin.pengaduan-kategori.edit', ['category' => $pengaduanKategori]);
    }

    public function update(Request $request, PengaduanKategori $pengaduanKategori): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nama kategori',
            'sort_order' => 'urutan',
        ]);

        $slug = $pengaduanKategori->slug;
        if ($pengaduanKategori->name !== $validated['name']) {
            $slug = PengaduanKategori::uniqueSlug($validated['name'], $pengaduanKategori->id);
        }

        $pengaduanKategori->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        Cache::forget('public_pengaduan_kategoris');

        return redirect()
            ->route('admin.pengaduan-kategori.index')
            ->with('success', 'Kategori pengaduan berhasil diperbarui.');
    }

    public function destroy(PengaduanKategori $pengaduanKategori): RedirectResponse
    {
        if ($pengaduanKategori->pengaduans()->exists()) {
            return redirect()
                ->route('admin.pengaduan-kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki pengaduan.');
        }

        $pengaduanKategori->delete();

        Cache::forget('public_pengaduan_kategoris');

        return redirect()
            ->route('admin.pengaduan-kategori.index')
            ->with('success', 'Kategori pengaduan berhasil dihapus.');
    }
}
