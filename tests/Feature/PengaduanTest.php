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
        $this->assertMatchesRegularExpression('/^PGD-\d{4}-\d{8}$/', $pengaduan->nomor);

        $response->assertRedirect(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]));
        $response->assertSessionHas('success');

        // Pelapor langsung bisa melihat pengaduannya tanpa mengulang verifikasi.
        $this->get(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertOk()
            ->assertSee('Proses KTP terlalu lama.');
    }

    public function test_pengaduan_detail_is_hidden_without_phone_verification(): void
    {
        $kategori = $this->kategori();

        $pengaduan = Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => PengaduanNomor::generate(),
            'nama' => 'Siti Rahma',
            'no_hp' => '081298765432',
            'isi' => 'Isi pengaduan rahasia.',
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        // Menebak nomor saja tidak boleh membuka detail.
        $this->get(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertOk()
            ->assertDontSee('Isi pengaduan rahasia.')
            ->assertDontSee('Siti Rahma')
            ->assertSee('belum dapat ditampilkan');
    }

    public function test_lookup_requires_matching_phone_number(): void
    {
        $kategori = $this->kategori();

        $pengaduan = Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => PengaduanNomor::generate(),
            'nama' => 'Siti Rahma',
            'no_hp' => '081298765432',
            'isi' => 'Isi pengaduan rahasia.',
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        $this->post(route('public.pengaduan.lookup-status'), [
            'nomor' => $pengaduan->nomor,
            'no_hp' => '081200000000',
        ])->assertRedirect(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertSessionHasErrors('no_hp');

        $this->get(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertOk()
            ->assertDontSee('Isi pengaduan rahasia.');
    }

    public function test_lookup_accepts_phone_in_different_format(): void
    {
        $kategori = $this->kategori();

        $pengaduan = Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => PengaduanNomor::generate(),
            'nama' => 'Siti Rahma',
            'no_hp' => '081298765432',
            'isi' => 'Isi pengaduan rahasia.',
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        $this->post(route('public.pengaduan.lookup-status'), [
            'nomor' => $pengaduan->nomor,
            'no_hp' => '+62 812-9876-5432',
        ])->assertRedirect(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertSessionHasNoErrors();

        $this->get(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertOk()
            ->assertSee('Isi pengaduan rahasia.');
    }

    public function test_pengaduan_input_is_escaped_in_public_and_admin_views(): void
    {
        $admin = $this->adminUser();
        $kategori = $this->kategori();

        $payload = '<b>tebal</b> <script>alert(1)</script> "kutip" & <img src=x onerror=alert(2)>';

        $pengaduan = Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => PengaduanNomor::generate(),
            'nama' => 'Pelapor Uji',
            'no_hp' => '081298765432',
            'isi' => $payload,
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        PengaduanFeedback::create([
            'pengaduan_id' => $pengaduan->id,
            'sender' => PengaduanFeedback::SENDER_USER,
            'user_id' => null,
            'message' => $payload,
        ]);

        $this->post(route('public.pengaduan.lookup-status'), [
            'nomor' => $pengaduan->nomor,
            'no_hp' => '081298765432',
        ]);

        // Payload harus tampil sebagai teks, bukan HTML aktif.
        $this->get(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(2)>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);

        $this->actingAs($admin)
            ->get(route('admin.pengaduan.show', $pengaduan))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(2)>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_public_feedback_requires_verified_session(): void
    {
        $kategori = $this->kategori();

        $pengaduan = Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => PengaduanNomor::generate(),
            'nama' => 'Siti Rahma',
            'no_hp' => '081298765432',
            'isi' => 'Isi pengaduan rahasia.',
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        $this->post(route('public.pengaduan.feedback'), [
            'nomor' => $pengaduan->nomor,
            'message' => 'Balasan tanpa verifikasi.',
        ])->assertSessionHasErrors('no_hp');

        $this->assertDatabaseMissing('pengaduan_feedbacks', [
            'pengaduan_id' => $pengaduan->id,
            'message' => 'Balasan tanpa verifikasi.',
        ]);
    }

    public function test_pengaduan_nomor_is_random_and_unique(): void
    {
        $kategori = $this->kategori();
        $nomor = [];

        for ($i = 0; $i < 20; $i++) {
            $generated = PengaduanNomor::generate();

            $this->assertMatchesRegularExpression('/^PGD-'.now()->year.'-\d{8}$/', $generated);
            $this->assertNotContains($generated, $nomor, 'Nomor pengaduan tidak boleh berulang.');

            Pengaduan::create([
                'pengaduan_kategori_id' => $kategori->id,
                'nomor' => $generated,
                'nama' => 'Pelapor',
                'no_hp' => '081234567890',
                'isi' => 'test',
                'status' => Pengaduan::STATUS_BARU,
                'submitted_at' => now(),
            ]);

            $nomor[] = $generated;
        }

        // Nomor tidak boleh sekuensial (mis. ...000001, ...000002).
        $suffixes = array_map(fn (string $n) => (int) substr($n, -PengaduanNomor::RANDOM_DIGITS), $nomor);
        $sorted = $suffixes;
        sort($sorted);

        $this->assertNotSame($sorted, $suffixes, 'Nomor pengaduan tidak boleh berurutan.');
    }

    public function test_legacy_sequential_numbers_still_work(): void
    {
        $kategori = $this->kategori();

        // Data lama di database tetap memakai format sekuensial.
        $pengaduan = Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => 'PGD-'.now()->year.'-000001',
            'nama' => 'Pelapor Lama',
            'no_hp' => '081298765432',
            'isi' => 'Pengaduan lama.',
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        $this->post(route('public.pengaduan.lookup-status'), [
            'nomor' => $pengaduan->nomor,
            'no_hp' => '081298765432',
        ])->assertRedirect(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertSessionHasNoErrors();

        $this->get(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]))
            ->assertOk()
            ->assertSee('Pengaduan lama.');
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

        // Pelapor memverifikasi nomor + no HP, lalu membalas feedback.
        $this->post(route('public.pengaduan.lookup-status'), [
            'nomor' => $pengaduan->nomor,
            'no_hp' => '08111',
        ])->assertRedirect(route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]));

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
