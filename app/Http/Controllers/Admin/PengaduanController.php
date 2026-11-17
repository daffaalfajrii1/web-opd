<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\PengaduanFeedback;
use App\Models\PengaduanKategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengaduanController extends Controller
{
    public function index(Request $request): View
    {
        $pengaduans = Pengaduan::query()
            ->with(['kategori', 'processor'])
            ->withCount('feedbacks')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('nomor', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('kategori_id'), fn ($q) => $q->where('pengaduan_kategori_id', $request->kategori_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        $kategoris = PengaduanKategori::query()->ordered()->get(['id', 'name']);

        return view('admin.pengaduan.index', compact('pengaduans', 'kategoris'));
    }

    public function show(Pengaduan $pengaduan): View
    {
        $pengaduan->load(['kategori', 'processor', 'feedbacks.admin']);

        return view('admin.pengaduan.show', compact('pengaduan'));
    }

    public function updateStatus(Request $request, Pengaduan $pengaduan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Pengaduan::STATUSES))],
        ], [], ['status' => 'status']);

        $pengaduan->update([
            'status' => $validated['status'],
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()
            ->route('admin.pengaduan.show', $pengaduan)
            ->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    public function storeFeedback(Request $request, Pengaduan $pengaduan): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ], [], ['message' => 'pesan feedback']);

        PengaduanFeedback::create([
            'pengaduan_id' => $pengaduan->id,
            'sender' => PengaduanFeedback::SENDER_ADMIN,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        if ($pengaduan->status === Pengaduan::STATUS_BARU) {
            $pengaduan->update([
                'status' => Pengaduan::STATUS_DIPROSES,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);
        } else {
            $pengaduan->update([
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);
        }

        return redirect()
            ->route('admin.pengaduan.show', $pengaduan)
            ->with('success', 'Feedback berhasil dikirim.');
    }
}
