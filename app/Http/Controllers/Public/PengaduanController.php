<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\PengaduanFeedback;
use App\Models\PengaduanKategori;
use App\Support\PengaduanAccess;
use App\Support\PengaduanNomor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaduanController extends Controller
{
    public function index(): View
    {
        $kategoris = PengaduanKategori::query()
            ->active()
            ->ordered()
            ->get();

        return view('public.pengaduan.index', compact('kategoris'));
    }

    public function show(string $slug): View
    {
        $kategori = PengaduanKategori::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $kategoris = PengaduanKategori::query()
            ->active()
            ->ordered()
            ->get(['id', 'name', 'slug']);

        return view('public.pengaduan.show', compact('kategori', 'kategoris'));
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $kategori = PengaduanKategori::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:30'],
            'isi' => ['required', 'string', 'max:5000'],
        ], [], [
            'nama' => 'nama',
            'no_hp' => 'nomor HP',
            'isi' => 'isi pengaduan',
        ]);

        $pengaduan = Pengaduan::create([
            'pengaduan_kategori_id' => $kategori->id,
            'nomor' => PengaduanNomor::generate(),
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'isi' => $validated['isi'],
            'status' => Pengaduan::STATUS_BARU,
            'submitted_at' => now(),
        ]);

        // Pelapor otomatis berhak melihat pengaduannya sendiri di session ini.
        PengaduanAccess::markVerified($pengaduan->nomor);

        return redirect()
            ->route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor])
            ->with('success', 'Pengaduan berhasil dikirim. Harap diingat nomor pengaduannya: '.$pengaduan->nomor);
    }

    public function checkStatus(Request $request): View
    {
        $pengaduan = null;
        $verified = false;
        $nomor = PengaduanNomor::normalize($request->query('nomor'));

        if ($nomor !== '' && PengaduanAccess::isVerified($nomor)) {
            $pengaduan = Pengaduan::query()
                ->with(['kategori', 'feedbacks.admin'])
                ->where('nomor', $nomor)
                ->first();

            $verified = $pengaduan !== null;
        }

        return view('public.pengaduan.check-status', compact('pengaduan', 'verified', 'nomor'));
    }

    public function lookupStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor' => ['required', 'string', 'max:50'],
            'no_hp' => ['required', 'string', 'max:30'],
        ], [], [
            'nomor' => 'nomor pengaduan',
            'no_hp' => 'nomor HP',
        ]);

        $nomor = PengaduanNomor::normalize($validated['nomor']);

        $pengaduan = Pengaduan::query()
            ->where('nomor', $nomor)
            ->first();

        // Pesan sengaja sama untuk nomor tidak ada maupun nomor HP salah,
        // agar nomor pengaduan tidak bisa diverifikasi lewat percobaan berulang.
        if (! $pengaduan || ! $pengaduan->matchesNoHp($validated['no_hp'])) {
            return redirect()
                ->route('public.pengaduan.check-status', ['nomor' => $nomor])
                ->withInput($request->only('nomor'))
                ->withErrors([
                    'no_hp' => 'Nomor pengaduan dan nomor HP tidak cocok. Periksa kembali keduanya.',
                ]);
        }

        PengaduanAccess::markVerified($pengaduan->nomor);

        return redirect()->route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor]);
    }

    public function storeFeedback(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
        ], [], [
            'nomor' => 'nomor pengaduan',
            'message' => 'pesan',
        ]);

        $nomor = PengaduanNomor::normalize($validated['nomor']);

        $pengaduan = Pengaduan::query()
            ->where('nomor', $nomor)
            ->first();

        // Jangan bedakan "nomor tidak ada" dan "belum diverifikasi"
        // agar keberadaan nomor pengaduan tidak bocor.
        if (! $pengaduan || ! PengaduanAccess::isVerified($pengaduan->nomor)) {
            return redirect()
                ->route('public.pengaduan.check-status', ['nomor' => $nomor])
                ->withErrors([
                    'no_hp' => 'Verifikasi nomor pengaduan dan nomor HP terlebih dahulu sebelum membalas.',
                ]);
        }

        PengaduanFeedback::create([
            'pengaduan_id' => $pengaduan->id,
            'sender' => PengaduanFeedback::SENDER_USER,
            'user_id' => null,
            'message' => $validated['message'],
        ]);

        return redirect()
            ->route('public.pengaduan.check-status', ['nomor' => $pengaduan->nomor])
            ->with('success', 'Balasan Anda berhasil dikirim.');
    }
}
