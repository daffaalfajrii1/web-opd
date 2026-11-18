<?php

namespace Tests\Feature;

use App\Models\LayananLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LayananLinkTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        Permission::findOrCreate('view dashboard', 'web');
        Permission::findOrCreate('manage link layanan', 'web');

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage link layanan']);

        return $user;
    }

    public function test_admin_can_create_link_layanan_with_logo(): void
    {
        Storage::fake('public');
        $user = $this->manager();

        $response = $this->actingAs($user)->post(route('admin.link-layanan.store'), [
            'title' => 'BPJS',
            'url' => 'https://www.bpjs-kesehatan.go.id',
            'description' => 'Portal BPJS',
            'is_active' => 1,
            'logo' => UploadedFile::fake()->image('bpjs.png', 120, 120),
        ]);

        $response->assertRedirect(route('admin.link-layanan.index'));
        $this->assertDatabaseHas('layanan_links', [
            'title' => 'BPJS',
            'url' => 'https://www.bpjs-kesehatan.go.id',
            'is_active' => 1,
        ]);

        $link = LayananLink::query()->first();
        $this->assertNotNull($link?->logo_path);
        Storage::disk('public')->assertExists($link->logo_path);
    }

    public function test_active_link_appears_on_homepage(): void
    {
        LayananLink::create([
            'title' => 'SIMRS',
            'url' => 'https://example.com/simrs',
            'description' => 'Sistem rumah sakit',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Link Layanan');
        $response->assertSee('SIMRS');
        $response->assertSee('https://example.com/simrs', false);
    }
}
