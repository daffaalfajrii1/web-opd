<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\BeritaCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeritaController extends Controller
{
    public function index(Request $request): View
    {
        $categories = BeritaCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $beritas = Berita::query()
            ->published()
            ->with(['category', 'author.roles'])
            ->when($request->filled('kategori'), function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->kategori));
            })
            ->when($request->search, fn ($q, $search) => $q->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            }))
            ->orderByDesc('published_at')
            ->paginate(6)
            ->withQueryString();

        return view('public.berita.index', compact('beritas', 'categories'));
    }

    public function show(string $slug): View
    {
        $berita = Berita::query()
            ->published()
            ->with(['category', 'author.roles'])
            ->where('slug', $slug)
            ->firstOrFail();

        $berita->increment('views_count');

        $related = Berita::query()
            ->published()
            ->where('id', '!=', $berita->id)
            ->when($berita->berita_category_id, fn ($q) => $q->where('berita_category_id', $berita->berita_category_id))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('public.berita.show', compact('berita', 'related'));
    }
}
