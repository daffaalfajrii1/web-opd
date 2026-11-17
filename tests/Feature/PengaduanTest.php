<?php

namespace Tests\Feature;

use App\Models\Pengaduan;
use App\Models\PengaduanFeedback;
use App\Models\PengaduanKategori;
use App\Models\User;
use App\Support\PengaduanNomor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PengaduanTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Permission::findOrCreate('view dashboard', 'web');
        Permission::findOrCreate('manage pengaduan', 'web');

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage pengaduan']);

        return $user;
    }

    private function kategori(array $overrides = []): PengaduanKategori
    {
        return PengaduanKategori::create(array_merge([
            'name' => 'Pelayanan',
            'slug' => 'pelayanan',
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    public function test_admin_can_create_kategori(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.pengaduan-kategori.store'), [
            'name' => 'Dokumen Kependudukan',
            'description' => 'Pengaduan terkait dokumen',
            'is_active' => 1,
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.pengaduan-kategori.index'));
        $this->assertDatabaseHas('pengaduan_kategoris', [
            'name' => 'Dokumen Kependudukan',
            'slug' => 'dokumen-kependudukan',
        ]);
    }

    public function test_public_can_submit_pengaduan_and_get_nomor(): void
    {
        $kategori = $this->kategori();

        $response = $this->post(route('public.pengaduan.store', $kategori->slug), [
            'nama' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'isi' => 'Proses KTP terlalu lama.',
        ]);

        $pengaduan = Pengaduan::query()->first();
        $this->assertNotNull($pengaduan);
        $this->assertMatchesRegularExpression('/^PGD-\d{4}-\d{6}$/', $pengaduan->nomor);

        $response->assertRedirect(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]));
        $response->assertSessionHas('success');
    }

    public function test_pengaduan_nomor_is_sequential(): void
    {
        $kategori = $this->kategori();

        $this->assertSame('PGD-'.now()->year.'-000001', PengaduanNomor::generate());

        Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => 'PGD-'.now()->year.'-000001',
            'nama' => 'A',
            'no_hp' => '081',
            'isi' => 'test',
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        $this->assertSame('PGD-'.now()->year.'-000002', PengaduanNomor::generate());
    }

    public function test_admin_and_user_can_exchange_feedback_by_nomor(): void
    {
        $admin = $this->adminUser();
        $kategori = $this->kategori();

        $pengaduan = Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => PengaduanNomor::generate(),
            'nama' => 'Siti',
            'no_hp' => '08111',
            'isi' => 'Ada kendala antrian',
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.pengaduan.feedback', $pengaduan), [
                'message' => 'Terima kasih, sedang kami proses.',
            ])
            ->assertRedirect(route('admin.pengaduan.show', $pengaduan));

        $this->assertDatabaseHas('pengaduan_feedbacks', [
            'pengaduan_id' => $pengaduan->id,
            'sender' => PengaduanFeedback::SENDER_ADMIN,
            'message' => 'Terima kasih, sedang kami proses.',
        ]);

        $this->assertSame(Pengaduan::STATUS_DIPROSES, $pengaduan->fresh()->status);

        $this->post(route('public.pengaduan.feedback'), [
            'nomor' => $pengaduan->nomor,
            'message' => 'Baik, saya tunggu kabar selanjutnya.',
        ])->assertRedirect(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]));

        $this->assertDatabaseHas('pengaduan_feedbacks', [
            'pengaduan_id' => $pengaduan->id,
            'sender' => PengaduanFeedback::SENDER_USER,
            'message' => 'Baik, saya tunggu kabar selanjutnya.',
        ]);

        $this->get(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertOk()
            ->assertSee('Harap diingat nomor pengaduannya')
            ->assertSee($pengaduan->nomor)
            ->assertSee('Terima kasih, sedang kami proses.')
            ->assertSee('Baik, saya tunggu kabar selanjutnya.');
    }

    public function test_navbar_shows_pengaduan_menu(): void
    {
        $this->kategori(['name' => 'Kartu Keluarga', 'slug' => 'kartu-keluarga']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Pengaduan')
            ->assertSee('Kartu Keluarga')
            ->assertSee(route('public.pengaduan.check-status'), false);
    }
}
