<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesEditorUpload;
use App\Http\Controllers\Controller;
use App\Models\NavMenu;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    use HandlesEditorUpload;

    public function index(Request $request): View
    {
        $pages = Page::query()
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($request->filled('menu_group'), fn ($q) => $q->where('menu_group', $request->menu_group))
            ->when($request->filled('is_published'), fn ($q) => $q->where('is_published', $request->boolean('is_published')))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    public function create(Request $request): View
    {
        return view('admin.pages.create', [
            'navMenus' => NavMenu::query()->ordered()->get(),
            'prefillNavMenuId' => $request->integer('nav_menu_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePage($request);

        $isPublished = $request->boolean('is_published', true);
        $menuGroup = $validated['menu_group'];
        $navMenuId = $menuGroup === Page::GROUP_NAV ? ($validated['nav_menu_id'] ?? null) : null;

        unset($validated['sort_order']);

        Page::create([
            ...$validated,
            'menu_group' => $menuGroup,
            'nav_menu_id' => $navMenuId,
            'sort_order' => ((int) Page::query()->where('nav_menu_id', $navMenuId)->max('sort_order')) + 1,
            'slug' => Page::uniqueSlug($validated['title'], $menuGroup),
            'user_id' => Auth::id(),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        Page::clearMenuCache();

        return redirect()
            ->route('admin.halaman.index')
            ->with('success', 'Halaman berhasil ditambahkan.');
    }

    public function edit(Page $halaman): View
    {
        return view('admin.pages.edit', [
            'page' => $halaman,
            'navMenus' => NavMenu::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request, Page $halaman): RedirectResponse
    {
        $validated = $this->validatePage($request);

        $isPublished = $request->boolean('is_published');
        $slug = $halaman->slug;
        $menuGroup = $validated['menu_group'];
        $navMenuId = $menuGroup === Page::GROUP_NAV ? ($validated['nav_menu_id'] ?? null) : null;

        if ($halaman->title !== $validated['title']) {
            $slug = Page::uniqueSlug($validated['title'], $menuGroup, $halaman->id);
        } elseif ($halaman->menu_group !== $menuGroup) {
            $slug = Page::uniqueSlug($validated['title'], $menuGroup, $halaman->id);
        }

        $halaman->update([
            ...$validated,
            'menu_group' => $menuGroup,
            'nav_menu_id' => $navMenuId,
            'slug' => $slug,
            'is_published' => $isPublished,
            'published_at' => $isPublished
                ? ($halaman->published_at ?? now())
                : null,
        ]);

        Page::clearMenuCache();

        return redirect()
            ->route('admin.halaman.index')
            ->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $halaman): RedirectResponse
    {
        $halaman->delete();
        Page::clearMenuCache();

        return redirect()
            ->route('admin.halaman.index')
            ->with('success', 'Halaman berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePage(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'menu_group' => ['required', 'string', 'in:'.implode(',', array_keys(Page::MENU_GROUPS))],
            'nav_menu_id' => [
                Rule::requiredIf($request->input('menu_group') === Page::GROUP_NAV),
                'nullable',
                'integer',
                'exists:nav_menus,id',
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ], [], [
            'title' => 'judul',
            'menu_group' => 'grup menu',
            'nav_menu_id' => 'menu navbar',
            'excerpt' => 'ringkasan',
            'content' => 'konten',
        ]);
    }
}
