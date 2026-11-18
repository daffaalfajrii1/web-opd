<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebsiteCarousel;
use App\Models\WebsiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class WebsiteSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage pengaturan', 'guard_name' => 'web']);
    }

    private function settingsManager(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo(['view dashboard', 'manage pengaturan']);

        return $user;
    }

    public function test_settings_manager_can_view_settings_page(): void
    {
        $response = $this->actingAs($this->settingsManager())->get(route('admin.pengaturan.edit'));

        $response->assertOk();
        $response->assertSee('Pengaturan Website');
        $response->assertSee('Identitas Website');
        $response->assertSee('Carousel Beranda');
        $response->assertSee('1920 × 600 px');
    }

    public function test_user_without_permission_cannot_access_settings(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo('view dashboard');

        $response = $this->actingAs($user)->get(route('admin.pengaturan.edit'));

        $response->assertForbidden();
    }

    public function test_settings_manager_can_update_site_identity(): void
    {
        $response = $this->actingAs($this->settingsManager())->put(route('admin.pengaturan.update'), [
            'site_name' => 'Satpol PP Rejang Lebong Baru',
            'site_tagline' => 'Melayani dengan hati',
            'theme_color' => WebsiteSetting::THEME_ORANGE,
            'carousel_duration' => 8,
            'carousel_enabled' => '1',
            'marquee_mode' => WebsiteSetting::MARQUEE_CUSTOM,
            'marquee_text' => 'Pengumuman penting hari ini',
            'marquee_enabled' => '1',
            'contact_address' => 'Jl. Baru No. 10',
            'contact_phone' => '0732000000',
            'contact_email' => 'baru@example.com',
            'footer_text' => 'Footer kustom',
        ]);

        $response->assertRedirect(route('admin.pengaturan.edit'));

        $settings = WebsiteSetting::query()->first();
        $this->assertSame('Satpol PP Rejang Lebong Baru', $settings->site_name);
        $this->assertSame('Melayani dengan hati', $settings->site_tagline);
        $this->assertSame(WebsiteSetting::THEME_ORANGE, $settings->theme_color);
    }

    public function test_public_home_uses_dynamic_site_name(): void
    {
        WebsiteSetting::query()->create(array_merge(WebsiteSetting::defaults(), [
            'site_name' => 'Nama Website Dinamis',
        ]));
        WebsiteSetting::clearCache();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Nama Website Dinamis');
    }

    public function test_settings_manager_can_upload_logo(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->settingsManager())->put(route('admin.pengaturan.update'), [
            'site_name' => 'Web OPD Rejang Lebong',
            'theme_color' => WebsiteSetting::THEME_TEAL,
            'carousel_duration' => 5,
            'marquee_mode' => WebsiteSetting::MARQUEE_CUSTOM,
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ]);

        $response->assertRedirect(route('admin.pengaturan.edit'));

        $settings = WebsiteSetting::query()->first();
        $this->assertNotNull($settings->logo_path);
        Storage::disk('public')->assertExists($settings->logo_path);
    }

    public function test_settings_manager_can_upload_and_delete_carousel(): void
    {
        Storage::fake('public');

        $manager = $this->settingsManager();

        $upload = $this->actingAs($manager)->post(route('admin.pengaturan.carousel.store'), [
            'images' => [
                UploadedFile::fake()->image('slide1.jpg', 1920, 600),
                UploadedFile::fake()->image('slide2.jpg', 1920, 600),
            ],
            'titles' => ['Slide Satu'],
        ]);

        $upload->assertRedirect(route('admin.pengaturan.edit'));
        $this->assertSame(2, WebsiteCarousel::count());

        $carousel = WebsiteCarousel::first();
        Storage::disk('public')->assertExists($carousel->image_path);

        $delete = $this->actingAs($manager)->delete(route('admin.pengaturan.carousel.destroy', $carousel));
        $delete->assertRedirect(route('admin.pengaturan.edit'));
        $this->assertSame(1, WebsiteCarousel::count());
        Storage::disk('public')->assertMissing($carousel->image_path);
    }

    public function test_settings_manager_can_change_theme_color(): void
    {
        WebsiteSetting::query()->create(WebsiteSetting::defaults());

        $response = $this->actingAs($this->settingsManager())->put(route('admin.pengaturan.update'), [
            'site_name' => 'Web OPD Rejang Lebong',
            'theme_color' => WebsiteSetting::THEME_RED,
            'carousel_duration' => 5,
            'marquee_mode' => WebsiteSetting::MARQUEE_CUSTOM,
        ]);

        $response->assertRedirect(route('admin.pengaturan.edit'));
        $this->assertSame(WebsiteSetting::THEME_RED, WebsiteSetting::query()->first()->theme_color);

        WebsiteSetting::clearCache();
        $home = $this->get(route('home'));
        $home->assertOk();
        $home->assertSee('--theme-primary: #b91c1c', false);
        $home->assertSee('--bg-dark-1: #b91c1c', false);
        $home->assertSee('id="opd-theme-vars"', false);
        $home->assertSee('.kecamatan-hero', false);
        $home->assertSee('linear-gradient(135deg, #7f1d1d 0%, #b91c1c 42%, #dc2626 100%)', false);
    }

    public function test_not_found_page_uses_branded_layout(): void
    {
        WebsiteSetting::query()->create(array_merge(WebsiteSetting::defaults(), [
            'site_name' => 'BPKD Rejang Lebong',
        ]));
        WebsiteSetting::clearCache();

        $response = $this->get('/halaman-yang-tidak-ada-xyz');

        $response->assertNotFound();
        $response->assertSee('Halaman tidak ditemukan');
        $response->assertSee('BPKD Rejang Lebong');
        $response->assertSee('Kembali ke Beranda');
    }

    public function test_public_home_shows_carousel_when_enabled(): void
    {
        Storage::fake('public');

        $settings = WebsiteSetting::query()->create(array_merge(WebsiteSetting::defaults(), [
            'carousel_enabled' => true,
            'carousel_duration' => 6,
        ]));

        $path = UploadedFile::fake()->image('banner.jpg', 1920, 600)->store('website/carousel', 'public');

        WebsiteCarousel::create([
            'image_path' => $path,
            'title' => 'Selamat Datang',
            'sort_order' => 1,
        ]);

        WebsiteSetting::clearCache();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('rsudCarousel', false);
        $response->assertSee('Selamat Datang');
        $response->assertSee('data-interval="6000"', false);
    }
}
