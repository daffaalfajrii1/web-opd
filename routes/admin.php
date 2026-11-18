<?php

use App\Http\Controllers\Admin\AgendaController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\BeritaCategoryController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\DokterSpesialisasiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\NavMenuController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\LayananPengajuanController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;

Route::middleware(['auth', 'verified', PermissionMiddleware::using('view dashboard')])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware(PermissionMiddleware::using('manage pegawai'))
            ->group(function () {
                Route::resource('jabatan', JabatanController::class)->except(['show']);
                Route::resource('pegawai', PegawaiController::class)->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage dokter'))
            ->group(function () {
                Route::resource('dokter-spesialisasi', DokterSpesialisasiController::class)
                    ->parameters(['dokter-spesialisasi' => 'dokterSpesialisasi'])
                    ->except(['show']);

                Route::post('dokter/{dokter}/jadwal', [DokterController::class, 'storeJadwal'])
                    ->name('dokter.jadwal.store');

                Route::put('dokter/{dokter}/jadwal/{jadwal}', [DokterController::class, 'updateJadwal'])
                    ->name('dokter.jadwal.update');

                Route::delete('dokter/{dokter}/jadwal/{jadwal}', [DokterController::class, 'destroyJadwal'])
                    ->name('dokter.jadwal.destroy');

                Route::resource('dokter', DokterController::class)->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage profil'))
            ->group(function () {
                Route::post('halaman/editor-upload', [PageController::class, 'uploadEditorImage'])
                    ->middleware('throttle:30,1')
                    ->name('halaman.editor-upload');

                Route::resource('halaman', PageController::class)
                    ->parameters(['halaman' => 'halaman'])
                    ->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage nav menu'))
            ->group(function () {
                Route::post('nav-menu/reorder', [NavMenuController::class, 'reorder'])
                    ->name('nav-menu.reorder');

                Route::post('nav-menu/{navMenu}/pages/reorder', [NavMenuController::class, 'reorderPages'])
                    ->name('nav-menu.pages.reorder');

                Route::resource('nav-menu', NavMenuController::class)
                    ->parameters(['nav-menu' => 'navMenu'])
                    ->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage berita'))
            ->group(function () {
                Route::post('berita/editor-upload', [BeritaController::class, 'uploadEditorImage'])
                    ->middleware('throttle:30,1')
                    ->name('berita.editor-upload');

                Route::resource('berita', BeritaController::class)
                    ->parameters(['berita' => 'berita'])
                    ->except(['show']);

                Route::resource('berita-kategori', BeritaCategoryController::class)
                    ->parameters(['berita-kategori' => 'beritaKategori'])
                    ->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage agenda'))
            ->group(function () {
                Route::post('agenda/editor-upload', [AgendaController::class, 'uploadEditorImage'])
                    ->middleware('throttle:30,1')
                    ->name('agenda.editor-upload');

                Route::resource('agenda', AgendaController::class)
                    ->parameters(['agenda' => 'agenda'])
                    ->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage galeri'))
            ->group(function () {
                Route::post('galeri/{galeri}/foto', [GaleriController::class, 'storePhotos'])
                    ->name('galeri.foto.store');

                Route::post('galeri/{galeri}/youtube', [GaleriController::class, 'storeYoutube'])
                    ->name('galeri.youtube.store');

                Route::delete('galeri/{galeri}/media/{item}', [GaleriController::class, 'destroyItem'])
                    ->name('galeri.media.destroy');

                Route::resource('galeri', GaleriController::class)
                    ->parameters(['galeri' => 'galeri'])
                    ->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage pengumuman'))
            ->group(function () {
                Route::post('pengumuman/editor-upload', [PengumumanController::class, 'uploadEditorImage'])
                    ->middleware('throttle:30,1')
                    ->name('pengumuman.editor-upload');

                Route::post('pengumuman/{pengumuman}/lampiran', [PengumumanController::class, 'storeAttachments'])
                    ->name('pengumuman.lampiran.store');

                Route::delete('pengumuman/{pengumuman}/lampiran/{attachment}', [PengumumanController::class, 'destroyAttachment'])
                    ->name('pengumuman.lampiran.destroy');

                Route::resource('pengumuman', PengumumanController::class)
                    ->parameters(['pengumuman' => 'pengumuman'])
                    ->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage layanan'))
            ->group(function () {
                Route::post('layanan/{layanan}/fields', [LayananController::class, 'storeField'])
                    ->name('layanan.fields.store');

                Route::put('layanan/{layanan}/fields/{field}', [LayananController::class, 'updateField'])
                    ->name('layanan.fields.update');

                Route::delete('layanan/{layanan}/fields/{field}', [LayananController::class, 'destroyField'])
                    ->name('layanan.fields.destroy');

                Route::resource('layanan', LayananController::class)
                    ->parameters(['layanan' => 'layanan'])
                    ->except(['show']);

                Route::get('layanan-pengajuan', [LayananPengajuanController::class, 'index'])
                    ->name('layanan-pengajuan.index');

                Route::get('layanan-pengajuan/{layananPengajuan}', [LayananPengajuanController::class, 'show'])
                    ->name('layanan-pengajuan.show');

                Route::patch('layanan-pengajuan/{layananPengajuan}/status', [LayananPengajuanController::class, 'updateStatus'])
                    ->name('layanan-pengajuan.status');
            });

        Route::middleware(PermissionMiddleware::using('manage users'))
            ->group(function () {
                Route::resource('pengguna', UserController::class)
                    ->parameters(['pengguna' => 'pengguna'])
                    ->except(['show']);
            });

        Route::middleware(PermissionMiddleware::using('manage roles'))
            ->prefix('roles')
            ->name('roles.')
            ->group(function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit');
                Route::put('{role}', [RoleController::class, 'update'])->name('update');
            });

        Route::middleware(PermissionMiddleware::using('manage pengaturan'))
            ->prefix('pengaturan')
            ->name('pengaturan.')
            ->group(function () {
                Route::get('/', [WebsiteSettingController::class, 'edit'])->name('edit');
                Route::put('/', [WebsiteSettingController::class, 'update'])->name('update');
                Route::post('carousel', [WebsiteSettingController::class, 'storeCarousel'])->name('carousel.store');
                Route::delete('carousel/{carousel}', [WebsiteSettingController::class, 'destroyCarousel'])->name('carousel.destroy');
            });
    });
