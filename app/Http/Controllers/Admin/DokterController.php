<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\DokterJadwal;
use App\Models\DokterSpesialisasi;
use App\Support\DokterDays;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DokterController extends Controller
{
    public function index(Request $request): View
    {
        $dokters = Dokter::query()
            ->with('spesialisasi')
            ->withCount('jadwals')
            ->when($request->search, fn ($q, $search) => $q->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('specialty', 'like', "%{$search}%")
                    ->orWhereHas('spesialisasi', fn ($sp) => $sp->where('name', 'like', "%{$search}%"));
            }))
            ->when($request->status === 'active', fn ($q) => $q->active())
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.dokter.index', compact('dokters'));
    }

    public function create(): View
    {
        return view('admin.dokter.create', [
            'spesialisasis' => DokterSpesialisasi::query()->active()->ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateDokter($request);
        $spesialisasi = DokterSpesialisasi::query()->findOrFail($validated['dokter_spesialisasi_id']);

        Dokter::create([
            'name' => $validated['name'],
            'slug' => Dokter::uniqueSlug($validated['name']),
            'dokter_spesialisasi_id' => $spesialisasi->id,
            'specialty' => $spesialisasi->name,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'poli' => $validated['poli'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'photo_path' => $this->storePhoto($request),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.dokter.index')
            ->with('success', 'Data dokter berhasil ditambahkan.');
    }

    public function edit(Dokter $dokter): View
    {
        $dokter->load(['jadwals' => fn ($q) => $q->ordered(), 'spesialisasi']);

        $spesialisasis = DokterSpesialisasi::query()->active()->ordered()->get();
        if ($dokter->spesialisasi && $spesialisasis->where('id', $dokter->dokter_spesialisasi_id)->isEmpty()) {
            $spesialisasis = $spesialisasis->prepend($dokter->spesialisasi)->values();
        }

        return view('admin.dokter.edit', [
            'dokter' => $dokter,
            'dayOptions' => DokterDays::DAYS,
            'spesialisasis' => $spesialisasis,
        ]);
    }

    public function update(Request $request, Dokter $dokter): RedirectResponse
    {
        $validated = $this->validateDokter($request);
        $spesialisasi = DokterSpesialisasi::query()->findOrFail($validated['dokter_spesialisasi_id']);

        $slug = $dokter->slug;
        if ($dokter->name !== $validated['name']) {
            $slug = Dokter::uniqueSlug($validated['name'], $dokter->id);
        }

        $data = [
            'name' => $validated['name'],
            'slug' => $slug,
            'dokter_spesialisasi_id' => $spesialisasi->id,
            'specialty' => $spesialisasi->name,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'poli' => $validated['poli'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->boolean('remove_photo') && $dokter->photo_path) {
            Storage::disk('public')->delete($dokter->photo_path);
            $data['photo_path'] = null;
        } elseif ($request->hasFile('photo')) {
            if ($dokter->photo_path) {
                Storage::disk('public')->delete($dokter->photo_path);
            }
            $data['photo_path'] = $this->storePhoto($request);
        }

        $dokter->update($data);

        return redirect()
            ->route('admin.dokter.edit', $dokter)
            ->with('success', 'Data dokter berhasil diperbarui.');
    }

    public function destroy(Dokter $dokter): RedirectResponse
    {
        if ($dokter->photo_path) {
            Storage::disk('public')->delete($dokter->photo_path);
        }

        $dokter->delete();

        return redirect()
            ->route('admin.dokter.index')
            ->with('success', 'Data dokter berhasil dihapus.');
    }

    public function storeJadwal(Request $request, Dokter $dokter): RedirectResponse
    {
        $validated = $this->validateJadwal($request);

        $dokter->jadwals()->create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.dokter.edit', $dokter)
            ->with('success', 'Jadwal praktik berhasil ditambahkan.');
    }

    public function updateJadwal(Request $request, Dokter $dokter, DokterJadwal $jadwal): RedirectResponse
    {
        $this->ensureJadwalBelongsToDokter($dokter, $jadwal);

        $validated = $this->validateJadwal($request);

        $jadwal->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.dokter.edit', $dokter)
            ->with('success', 'Jadwal praktik berhasil diperbarui.');
    }

    public function destroyJadwal(Dokter $dokter, DokterJadwal $jadwal): RedirectResponse
    {
        $this->ensureJadwalBelongsToDokter($dokter, $jadwal);

        $jadwal->delete();

        return redirect()
            ->route('admin.dokter.edit', $dokter)
            ->with('success', 'Jadwal praktik berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validateDokter(Request $request): array
    {
        foreach (['phone', 'email', 'facebook_url', 'poli', 'bio'] as $optional) {
            if ($request->input($optional) === '') {
                $request->merge([$optional => null]);
            }
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'dokter_spesialisasi_id' => ['required', 'integer', 'exists:dokter_spesialisasis,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'poli' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nama dokter',
            'dokter_spesialisasi_id' => 'spesialisasi',
            'phone' => 'telepon',
            'email' => 'email',
            'facebook_url' => 'facebook',
            'poli' => 'poli / unit',
            'photo' => 'foto dokter',
        ]);
    }

    /** @return array<string, mixed> */
    private function validateJadwal(Request $request): array
    {
        return $request->validate([
            'day_of_week' => ['required', 'integer', Rule::in(DokterDays::dayNumbers())],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'note' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'day_of_week' => 'hari',
            'start_time' => 'jam mulai',
            'end_time' => 'jam selesai',
        ]);
    }

    private function storePhoto(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        return $request->file('photo')->store('dokters', 'public');
    }

    private function ensureJadwalBelongsToDokter(Dokter $dokter, DokterJadwal $jadwal): void
    {
        if ($jadwal->dokter_id !== $dokter->id) {
            abort(404);
        }
    }
}