@extends('admin.layouts.app')

@section('title', 'Link Layanan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Link Layanan',
        'crumbs' => [['label' => 'Link Layanan']],
        'actions' => '<a href="'.route('admin.link-layanan.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Link</a>',
    ])

    <div class="main-content">
        <div class="alert alert-light border mb-4">
            Link + logo yang ditambahkan di sini akan tampil di beranda publik (bagian bawah).
        </div>

        <div class="card kecamatan-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width:70px">Logo</th>
                                <th>Nama</th>
                                <th>Tautan</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($links as $link)
                                <tr>
                                    <td>
                                        <img src="{{ $link->logoUrl() }}" alt="{{ $link->title }}" style="width:48px;height:48px;object-fit:contain;border-radius:8px;background:#f5f7f8;padding:4px">
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $link->title }}</div>
                                        @if ($link->description)
                                            <small class="text-muted">{{ $link->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ $link->url }}" target="_blank" rel="noopener" class="text-break">{{ \Illuminate\Support\Str::limit($link->url, 48) }}</a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $link->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                            {{ $link->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.link-layanan.edit', $link) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.link-layanan.destroy', $link) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus link layanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada link layanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $links->links() }}</div>
            </div>
        </div>
    </div>
@endsection
