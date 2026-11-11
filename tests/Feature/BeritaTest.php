<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\BeritaCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BeritaTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage berita', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage berita']);

        return $user;
    }

    private function category(): BeritaCategory
    {
        return BeritaCategory::create([
            'name' => 'Umum',
            'slug' => 'umum',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_berita_index(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get(route('admin.berita.index'));

        $response->assertOk();
        $response->assertSee('Berita');
    }

    public function test_admin_can_create_berita_draft(): void
    {
        $user = $this->adminUser();
        $category = $this->category();

        $response = $this->actingAs($user)->post(route('admin.berita.store'), [
            'berita_category_id' => $category->id,
            'title' => 'Peluncuran Program Desa',
            'excerpt' => 'Ringkasan berita',
            'content' => '<p>Konten berita lengkap</p>',
            'status' => Berita::STATUS_DRAFT,
        ]);

        $response->assertRedirect(route('admin.berita.index'));
        $this->assertDatabaseHas('beritas', [
            'title' => 'Peluncuran Program Desa',
            'slug' => 'peluncuran-program-desa',
            'status' => Berita::STATUS_DRAFT,
            'user_id' => $user->id,
            'views_count' => 0,
        ]);
    }

    public function test_admin_can_publish_berita(): void
    {
        $user = $this->adminUser();
        $category = $this->category();

        $response = $this->actingAs($user)->post(route('admin.berita.store'), [
            'berita_category_id' => $category->id,
            'title' => 'Berita Terbit',
            'content' => '<p>Isi</p>',
            'status' => Berita::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect(route('admin.berita.index'));
        $berita = Berita::where('title', 'Berita Terbit')->first();
        $this->assertNotNull($berita->published_at);
        $this->assertSame(Berita::STATUS_PUBLISHED, $berita->status);
    }

    public function test_admin_edit_berita_includes_rich_text_editor(): void
    {
        $user = $this->adminUser();
        $category = $this->category();

        $berita = Berita::create([
            'berita_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Berita Test',
            'slug' => 'berita-test',
            'content' => '<p>Test</p>',
            'status' => Berita::STATUS_DRAFT,
            'views_count' => 42,
        ]);

        $response = $this->actingAs($user)->get(route('admin.berita.edit', $berita));

        $response->assertOk();
        $response->assertSee('tinymce.min.js', false);
        $response->assertSee('id="berita-content"', false);
        $response->assertSee('42', false);
    }

    public function test_admin_can_upload_berita_editor_image(): void
    {
        $user = $this->adminUser();
        $file = \Illuminate\Http\UploadedFile::fake()->image('foto.jpg', 800, 600);

        $response = $this->actingAs($user)->postJson(route('admin.berita.editor-upload'), [
            'file' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['url', 'location']);
    }

    public function test_admin_can_manage_berita_category(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.berita-kategori.store'), [
            'name' => 'Sosial',
            'description' => 'Berita sosial',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.berita-kategori.index'));
        $this->assertDatabaseHas('berita_categories', [
            'name' => 'Sosial',
            'slug' => 'sosial',
            'is_active' => true,
        ]);
    }

    public function test_category_with_berita_cannot_be_deleted(): void
    {
        $user = $this->adminUser();
        $category = $this->category();

        Berita::create([
            'berita_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Berita Terkait',
            'slug' => 'berita-terkait',
            'status' => Berita::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.berita-kategori.destroy', $category));

        $response->assertRedirect(route('admin.berita-kategori.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('berita_categories', ['id' => $category->id]);
    }

    public function test_public_berita_show_includes_open_graph_meta(): void
    {
        $user = $this->adminUser();
        $category = $this->category();

        $berita = Berita::create([
            'berita_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Berita Open Graph',
            'slug' => 'berita-open-graph',
            'excerpt' => 'Ringkasan berita untuk share sosial media',
            'content' => '<p>Konten lengkap berita</p>',
            'thumbnail' => 'berita/thumbnail-og.jpg',
            'status' => Berita::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.berita.show', $berita->slug));

        $response->assertOk();
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('storage/berita/thumbnail-og.jpg', false);
        $response->assertSee('property="og:type" content="article"', false);
        $response->assertSee('name="twitter:card" content="summary_large_image"', false);
        $response->assertSee('Ringkasan berita untuk share sosial media');
    }
}
