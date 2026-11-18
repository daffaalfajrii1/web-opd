<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavMenu;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NavMenuController extends Controller
{
    public function index(): View
    {
        $menus = NavMenu::query()
            ->withCount(['pages'])
            ->ordered()
            ->get();

        return view('admin.nav-menu.index', compact('menus'));
    }

    public function create(): View
    {
        return view('admin.nav-menu.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['title' => 'judul menu']);

        NavMenu::create([
            'title' => $validated['title'],
            'slug' => NavMenu::uniqueSlug($validated['title']),
            'sort_order' => ((int) NavMenu::query()->max('sort_order')) + 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        Page::clearMenuCache();

        return redirect()
            ->route('admin.nav-menu.index')
            ->with('success', 'Menu navbar berhasil ditambahkan.');
    }

    public function edit(NavMenu $navMenu): View
    {
        $navMenu->load(['pages' => fn ($q) => $q->ordered()]);

        return view('admin.nav-menu.edit', ['menu' => $navMenu]);
    }

    public function update(Request $request, NavMenu $navMenu): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['title' => 'judul menu']);

        $slug = $navMenu->slug;
        if ($navMenu->title !== $validated['title']) {
            $slug = NavMenu::uniqueSlug($validated['title'], $navMenu->id);
        }

        $navMenu->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'is_active' => $request->boolean('is_active'),
        ]);

        Page::clearMenuCache();

        return redirect()
            ->route('admin.nav-menu.index')
            ->with('success', 'Menu navbar berhasil diperbarui.');
    }

    public function destroy(NavMenu $navMenu): RedirectResponse
    {
        if ($navMenu->pages()->exists()) {
            return back()->with('error', 'Menu masih memiliki halaman. Pindahkan/hapus halaman dulu.');
        }

        $navMenu->delete();
        Page::clearMenuCache();

        return redirect()
            ->route('admin.nav-menu.index')
            ->with('success', 'Menu navbar berhasil dihapus.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:nav_menus,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            NavMenu::query()->where('id', $id)->update(['sort_order' => $index + 1]);
        }

        Page::clearMenuCache();

        return back()->with('success', 'Urutan menu berhasil disimpan.');
    }

    public function reorderPages(Request $request, NavMenu $navMenu): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:pages,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            Page::query()
                ->where('id', $id)
                ->where('nav_menu_id', $navMenu->id)
                ->update(['sort_order' => $index + 1]);
        }

        Page::clearMenuCache();

        return back()->with('success', 'Urutan halaman dropdown berhasil disimpan.');
    }
}
