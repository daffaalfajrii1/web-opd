<?php

namespace Tests\Feature;

use App\Models\NavMenu;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class NavMenuTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        Permission::findOrCreate('view dashboard', 'web');
        Permission::findOrCreate('manage nav menu', 'web');

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage nav menu']);

        return $user;
    }

    public function test_admin_can_delete_menu_with_submenus(): void
    {
        $user = $this->manager();

        $menu = NavMenu::create([
            'title' => 'ASD',
            'slug' => 'asd',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Page::create([
            'title' => 'Sub Satu',
            'slug' => 'sub-satu',
            'menu_group' => Page::GROUP_NAV,
            'nav_menu_id' => $menu->id,
            'content' => '<p>isi</p>',
            'is_published' => true,
            'sort_order' => 1,
            'user_id' => $user->id,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->delete(route('admin.nav-menu.destroy', $menu));

        $response->assertRedirect(route('admin.nav-menu.index'));
        $this->assertDatabaseMissing('nav_menus', ['id' => $menu->id]);
        $this->assertDatabaseMissing('pages', ['slug' => 'sub-satu', 'menu_group' => Page::GROUP_NAV]);
    }

    public function test_public_menu_page_breadcrumb_uses_nav_menu_title(): void
    {
        $menu = NavMenu::create([
            'title' => 'ASD',
            'slug' => 'asd',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $page = Page::create([
            'title' => '123123',
            'slug' => '123123',
            'menu_group' => Page::GROUP_NAV,
            'nav_menu_id' => $menu->id,
            'content' => '<p>konten</p>',
            'is_published' => true,
            'sort_order' => 1,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.menu-page.show', [$menu->slug, $page->slug]));

        $response->assertOk();
        $response->assertSee('ASD');
        $response->assertSee('123123');
        $response->assertDontSee('>PROFIL<', false);
    }
}
