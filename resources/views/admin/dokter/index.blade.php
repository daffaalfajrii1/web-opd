@extends('admin.layouts.app')

@section('title', 'Dokter')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Data Dokter',
        'crumbs' => [['label' => 'Dokter']],
        'actions' => '<a href="'.route('admin.dokter-spesialisasi.index').'" class="btn btn-light-brand me-2"><i class="feather-tag me-2"></i>Spesialisasi</a><a href="'.route('admin.dokter.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Dokter</a>',
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="fw-semibold fs-13">Cari Nama / Spesialisasi</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama atau spesialisasi...">
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold fs-13">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="active" @selected(request('status') === 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-light-brand w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card kecamatan-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Spesialisasi</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokters as $dokter)
                                <tr>
                                    <td>{{ $dokters->firstItem() + $loop->index }}</td>
                                    <td>
                                        @if ($dokter->photoUrl())
                                            <img src="{{ $dokter->photoUrl() }}" alt="{{ $dokter->name }}" class="rounded" width="48" height="48" style="object-fit:cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center fw-bold text-muted" style="width:48px;height:48px;">{{ $dokter->initials() }}</div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $dokter->name }}</td>
                                    <td>{{ $dokter->specialtyLabel() }}</td>
                                    <td>{{ $dokter->jadwals_count }} jadwal</td>
                                    <td>
                                        <span class="badge {{ $dokter->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                            {{ $dokter->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            <a href="{{ route('admin.dokter.edit', $dokter) }}" class="btn btn-sm btn-primary px-2" title="Edit dokter">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <form action="{{ route('admin.dokter.destroy', $dokter) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokter ini beserta jadwalnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Hapus dokter">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada data dokter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $dokters->links() }}</div>
            </div>
        </div>
    </div>
@endsection