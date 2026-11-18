<?php

namespace App\Http\View\Composers;

use App\Models\NavMenu;
use App\Models\Page;
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

        if (! Schema::hasTable('nav_menus')) {
            $view->with('navMenus', collect());

            return;
        }

        $view->with(
            'navMenus',
            Cache::remember('public_nav_menus', 600, function () {
                return NavMenu::query()
                    ->active()
                    ->ordered()
                    ->with(['pages' => fn ($q) => $q->published()->ordered()->select(['id', 'title', 'slug', 'nav_menu_id', 'sort_order'])])
                    ->get();
            })
        );
    }
}
