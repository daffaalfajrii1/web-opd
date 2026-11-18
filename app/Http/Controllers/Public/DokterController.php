<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\DokterJadwal;
use App\Support\DokterDays;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DokterController extends Controller
{
    public function index(Request $request): View
    {
        $dokters = Dokter::query()
            ->with('spesialisasi')
            ->active()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->string('q')->toString();
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('specialty', 'like', "%{$term}%")
                        ->orWhereHas('spesialisasi', fn ($sp) => $sp->where('name', 'like', "%{$term}%"));
                });
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('public.dokter.index', compact('dokters'));
    }

    public function show(string $slug): View
    {
        $dokter = Dokter::query()
            ->active()
            ->where('slug', $slug)
            ->with([
                'spesialisasi',
                'jadwals' => fn ($q) => $q->active()->ordered(),
            ])
            ->firstOrFail();

        return view('public.dokter.show', compact('dokter'));
    }

    public function jadwal(Request $request): View
    {
        $jadwalsQuery = DokterJadwal::query()
            ->active()
            ->whereHas('dokter', fn ($q) => $q->active())
            ->with(['dokter' => fn ($q) => $q->with('spesialisasi')->active()->ordered()])
            ->ordered();

        if ($request->filled('dokter_id')) {
            $jadwalsQuery->where('dokter_id', $request->integer('dokter_id'));
        }

        if ($request->filled('day')) {
            $jadwalsQuery->where('day_of_week', $request->integer('day'));
        }

        $jadwals = $jadwalsQuery->get();

        $groupedByDay = $jadwals->groupBy('day_of_week')->sortKeys();

        $dokters = Dokter::query()
            ->with('spesialisasi')
            ->active()
            ->ordered()
            ->get(['id', 'name', 'slug', 'specialty', 'dokter_spesialisasi_id']);
        $dayOptions = DokterDays::DAYS;

        return view('public.dokter.jadwal', compact('groupedByDay', 'dokters', 'dayOptions'));
    }
}