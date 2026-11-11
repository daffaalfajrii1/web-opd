<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\View\View;

class GaleriController extends Controller
{
    public function index(): View
    {
        $galeris = Galeri::query()
            ->published()
            ->orderByDesc('is_featured')
            ->orderByDesc('event_date')
            ->orderByDesc('published_at')
            ->paginate(6);

        return view('public.galeri.index', compact('galeris'));
    }

    public function show(string $slug): View
    {
        $galeri = Galeri::query()
            ->published()
            ->with(['photos', 'videos'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.galeri.show', compact('galeri'));
    }
}
