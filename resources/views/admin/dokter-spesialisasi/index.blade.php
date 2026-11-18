@extends('admin.layouts.app')

@section('title', 'Spesialisasi Dokter')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Spesialisasi Dokter',
        'crumbs' => [
            ['label' => 'Dokter', 'url' => route('admin.dokter.index')],
            ['label' => 'Spesialisasi'],
        ],
        'actions' => '<a href="'.route('admin.dokter-spesialisasi.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Spesialisasi</a>',
    ])

    <div class="main-content">
        <div class="card kecamatan-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Nama</th>
                                <th>Jumlah Dokter</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($spesialisasis as $item)
                                <tr>
                                    <td>{{ $spesialisasis->firstItem() + $loop->index }}</td>
                                    <td class="fw-semibold">{{ $item->name }}</td>
                                    <td>{{ $item->dokters_count }}</td>
                                    <td>
                                        <span class="badge {{ $item->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.dokter-spesialisasi.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.dokter-spesialisasi.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus spesialisasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" @disabled($item->dokters_count > 0)>Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada spesialisasi. Tambahkan dulu sebelum membuat data dokter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $spesialisasis->links() }}</div>
            </div>
        </div>
    </div>
@endsection
