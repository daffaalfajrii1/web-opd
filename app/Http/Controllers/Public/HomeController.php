<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Dokter;
use App\Models\Galeri;
use App\Models\Layanan;
use App\Models\LayananLink;
use App\Models\Pegawai;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $latestBeritas = Berita::query()
            ->published()
            ->with(['category', 'author.roles'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $upcomingAgendas = Agenda::query()
            ->published()
            ->upcoming()
            ->orderBy('event_date')
            ->limit(3)
            ->get();

        $latestPengumumans = Pengumuman::query()
            ->published()
            ->withCount('attachments')
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $featuredGaleris = Galeri::query()
            ->published()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        $layanans = Layanan::query()
            ->active()
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $layananLinks = collect();
        if (Schema::hasTable('layanan_links')) {
            $layananLinks = LayananLink::query()
                ->active()
                ->ordered()
                ->get();
        }

        $totalPegawai = Pegawai::query()->published()->count();

        $featuredDokters = collect();
        $totalDokter = 0;
        if (Schema::hasTable('dokters')) {
            $totalDokter = Dokter::query()->active()->count();
            $featuredDokters = Dokter::query()
                ->with('spesialisasi')
                ->active()
                ->ordered()
                ->limit(6)
                ->get();
        }

        return view('public.home', compact(
            'latestBeritas',
            'upcomingAgendas',
            'latestPengumumans',
            'featuredGaleris',
            'layanans',
            'layananLinks',
            'totalPegawai',
            'totalDokter',
            'featuredDokters',
        ));
    }
}
