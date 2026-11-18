<?php

namespace Tests\Feature;

use App\Models\Dokter;
use App\Models\DokterSpesialisasi;
use App\Models\User;
use App\Support\DokterDays;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DokterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage dokter', 'guard_name' => 'web']);
    }

    private function manager(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo(['view dashboard', 'manage dokter']);

        return $user;
    }

    public function test_admin_can_create_dokter_with_jadwal(): void
    {
        Storage::fake('public');

        $user = $this->manager();

        $spesialisasi = DokterSpesialisasi::create([
            'name' => 'Spesialis Jantung',
            'slug' => 'spesialis-jantung',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('admin.dokter.store'), [
            'name' => 'dr Andi Wijaya',
            'dokter_spesialisasi_id' => $spesialisasi->id,
            'phone' => '082112345678',
            'email' => 'andi@example.com',
            'facebook_url' => 'https://facebook.com/drandi',
            'poli' => 'Poli Jantung',
            'bio' => 'Dokter spesialis jantung',
            'sort_order' => 1,
            'is_active' => '1',
            'photo' => UploadedFile::fake()->image('dokter.jpg', 800, 1000),
        ]);

        $response->assertRedirect(route('admin.dokter.index'));

        $dokter = Dokter::first();
        $this->assertNotNull($dokter);
        $this->assertSame('dr-andi-wijaya', $dokter->slug);
        $this->assertSame('082112345678', $dokter->phone);
        $this->assertSame('Poli Jantung', $dokter->poli);
        Storage::disk('public')->assertExists($dokter->photo_path);

        $jadwalResponse = $this->actingAs($user)->post(route('admin.dokter.jadwal.store', $dokter), [
            'day_of_week' => DokterDays::SENIN,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'note' => 'Poli pagi',
            'is_active' => '1',
        ]);

        $jadwalResponse->assertRedirect(route('admin.dokter.edit', $dokter));

        $this->assertDatabaseHas('dokter_jadwals', [
            'dokter_id' => $dokter->id,
            'day_of_week' => 1,
            'note' => 'Poli pagi',
        ]);
    }

    public function test_admin_must_create_spesialisasi_before_dokter_dropdown(): void
    {
        $user = $this->manager();

        $createPage = $this->actingAs($user)->get(route('admin.dokter.create'));
        $createPage->assertOk();
        $createPage->assertSee('Tambah spesialisasi dulu');

        $store = $this->actingAs($user)->post(route('admin.dokter.store'), [
            'name' => 'dr Tanpa Spesialisasi',
            'is_active' => '1',
        ]);
        $store->assertSessionHasErrors('dokter_spesialisasi_id');

        $this->actingAs($user)->post(route('admin.dokter-spesialisasi.store'), [
            'name' => 'Spesialis Orthopedi',
            'sort_order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.dokter-spesialisasi.index'));

        $this->assertDatabaseHas('dokter_spesialisasis', [
            'name' => 'Spesialis Orthopedi',
            'slug' => 'spesialis-orthopedi',
        ]);

        $createPageAfter = $this->actingAs($user)->get(route('admin.dokter.create'));
        $createPageAfter->assertOk();
        $createPageAfter->assertSee('Spesialis Orthopedi');
        $createPageAfter->assertDontSee('Tambah spesialisasi dulu');
    }

    public function test_public_dokter_index_supports_search(): void
    {
        $dalam = DokterSpesialisasi::create([
            'name' => 'Penyakit Dalam',
            'slug' => 'penyakit-dalam',
            'is_active' => true,
        ]);
        $anak = DokterSpesialisasi::create([
            'name' => 'Anak',
            'slug' => 'anak',
            'is_active' => true,
        ]);

        Dokter::create([
            'name' => 'Dr. Budi',
            'slug' => 'dr-budi',
            'dokter_spesialisasi_id' => $dalam->id,
            'specialty' => $dalam->name,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Dokter::create([
            'name' => 'Dr. Citra',
            'slug' => 'dr-citra',
            'dokter_spesialisasi_id' => $anak->id,
            'specialty' => $anak->name,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('public.dokter.index', ['q' => 'Penyakit']));

        $response->assertOk();
        $response->assertSee('Dr. Budi');
        $response->assertDontSee('Dr. Citra');
    }
}