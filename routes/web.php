<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\AgendaController;
use App\Http\Controllers\Public\BeritaController;
use App\Http\Controllers\Public\DokterController as PublicDokterController;
use App\Http\Controllers\Public\GaleriController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\KontakController;
use App\Http\Controllers\Public\LayananController;
use App\Http\Controllers\Public\MenuPageController;
use App\Http\Controllers\Public\PageController as PublicPageController;
use App\Http\Controllers\Public\PegawaiController as PublicPegawaiController;
use App\Http\Controllers\Public\PengumumanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profil/{slug}', [PublicPageController::class, 'show'])->name('public.profil.show');
Route::get('/menu/{menu}/{slug}', [MenuPageController::class, 'show'])->name('public.menu-page.show');
Route::get('/pegawai', [PublicPegawaiController::class, 'index'])->name('public.pegawai.index');

Route::get('/dokter', [PublicDokterController::class, 'index'])->name('public.dokter.index');
Route::get('/dokter/{slug}', [PublicDokterController::class, 'show'])->name('public.dokter.show');
Route::get('/jadwal-dokter', [PublicDokterController::class, 'jadwal'])->name('public.dokter.jadwal');

Route::get('/berita', [BeritaController::class, 'index'])->name('public.berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('public.berita.show');

Route::get('/agenda', [AgendaController::class, 'index'])->name('public.agenda.index');
Route::get('/agenda/{slug}', [AgendaController::class, 'show'])->name('public.agenda.show');

Route::get('/galeri', [GaleriController::class, 'index'])->name('public.galeri.index');
Route::get('/galeri/{slug}', [GaleriController::class, 'show'])->name('public.galeri.show');

Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('public.pengumuman.index');
Route::get('/pengumuman/{slug}', [PengumumanController::class, 'show'])->name('public.pengumuman.show');

Route::get('/layanan', [LayananController::class, 'index'])->name('public.layanan.index');
Route::get('/layanan/cek-status', [LayananController::class, 'checkStatus'])->name('public.layanan.check-status');
Route::post('/layanan/cek-status', [LayananController::class, 'lookupStatus'])
    ->middleware('throttle:public-form')
    ->name('public.layanan.lookup-status');
Route::get('/layanan/{slug}', [LayananController::class, 'show'])->name('public.layanan.show');
Route::post('/layanan/{slug}', [LayananController::class, 'store'])
    ->middleware('throttle:public-form')
    ->name('public.layanan.store');

Route::get('/kontak', [KontakController::class, 'index'])->name('public.kontak.index');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
