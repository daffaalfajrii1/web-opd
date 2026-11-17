<?php

namespace App\Http\View\Composers;

use App\Models\Page;
use App\Models\PengaduanKategori;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicMenuComposer
{
    public function compose(View $view): void
    {
        if (! Schema::hasTable('pages')) {
            $view->with('profilPages', collect());
        } else {
            $view->with(
                'profilPages',
                Cache::remember('public_profil_pages', 3600, function () {
                    return Page::query()
                        ->published()
                        ->profil()
                        ->ordered()
                        ->get(['id', 'title', 'slug']);
                })
            );
        }

        if (! Schema::hasTable('pengaduan_kategoris')) {
            $view->with('pengaduanKategoris', collect());

            return;
        }

        $view->with(
            'pengaduanKategoris',
            Cache::remember('public_pengaduan_kategoris', 600, function () {
                return PengaduanKategori::query()
                    ->active()
                    ->ordered()
                    ->get(['id', 'name', 'slug']);
            })
        );
    }
}
