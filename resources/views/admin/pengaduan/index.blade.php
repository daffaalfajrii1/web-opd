@extends('admin.layouts.app')

@section('title', 'Pengaduan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Daftar Pengaduan',
        'crumbs' => [
            ['label' => 'Pengaduan'],
        ],
        'actions' => '<a href="'.route('admin.pengaduan-kategori.index').'" class="btn btn-light-brand"><i class="feather-tag me-2"></i>Kategori</a>',
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Cari</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nomor / nama / HP">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Kategori</label>
                        <select name="kategori_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" @selected(request('kategori_id') == $kategori->id)>
                                    {{ $kategori->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            @foreach (App\Models\Pengaduan::STATUSES as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-light-brand w-100">
                            <i class="feather-filter me-1"></i> Filter
                        </button>
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
                                <th>Nomor</th>
                                <th>Pelapor</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Feedback</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengaduans as $item)
                                <tr>
                                    <td>{{ $pengaduans->firstItem() + $loop->index }}</td>
                                    <td><code class="fw-semibold">{{ $item->nomor }}</code></td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->nama }}</div>
                                        <div class="text-muted fs-12">{{ $item->no_hp }}</div>
                                        <div class="text-muted fs-12">{{ $item->submitted_at?->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td>{{ $item->kategori?->name }}</td>
                                    <td>
                                        @php
                                            $badge = match ($item->status) {
                                                App\Models\Pengaduan::STATUS_SELESAI => 'bg-soft-success text-success',
                                                App\Models\Pengaduan::STATUS_DIPROSES => 'bg-soft-warning text-warning',
                                                default => 'bg-soft-primary text-primary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ $item->statusLabel() }}</span>
                                    </td>
                                    <td>{{ $item->feedbacks_count }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.pengaduan.show', $item) }}" class="btn btn-sm btn-primary">
                                            <i class="feather-eye"></i>
                                            <span class="d-none d-xl-inline ms-1">Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada pengaduan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $pengaduans->links() }}</div>
            </div>
        </div>
    </div>
@endsection
