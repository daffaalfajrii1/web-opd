<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NavMenu;
use App\Models\Page;
use Illuminate\View\View;

class MenuPageController extends Controller
{
    public function show(string $menu, string $slug): View
    {
        $navMenu = NavMenu::query()
            ->active()
            ->where('slug', $menu)
            ->firstOrFail();

        $page = Page::query()
            ->published()
            ->where('menu_group', Page::GROUP_NAV)
            ->where('nav_menu_id', $navMenu->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $navMenu->load(['pages' => fn ($q) => $q->published()->ordered()->select(['id', 'title', 'slug', 'nav_menu_id', 'sort_order'])]);

        return view('public.pages.show', compact('page', 'navMenu'));
    }
}
