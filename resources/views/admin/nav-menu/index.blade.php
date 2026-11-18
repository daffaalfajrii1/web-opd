@extends('admin.layouts.app')

@section('title', 'Menu Navbar')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Menu Navbar Dinamis',
        'crumbs' => [['label' => 'Menu Navbar']],
        'actions' => '<a href="'.route('admin.nav-menu.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Menu</a>',
    ])

    <div class="main-content">
        <div class="alert alert-light border">
            Drag baris untuk mengubah urutan dropdown di navbar, lalu klik <strong>Simpan Urutan</strong>.
        </div>

        <form method="POST" action="{{ route('admin.nav-menu.reorder') }}">
            @csrf
            <div class="card kecamatan-table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="width:40px"></th>
                                    <th>Judul</th>
                                    <th>Slug</th>
                                    <th>Halaman</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="nav-menu-sortable">
                                @forelse ($menus as $menu)
                                    <tr data-id="{{ $menu->id }}" style="cursor:grab">
                                        <td><i class="feather-menu text-muted"></i><input type="hidden" name="order[]" value="{{ $menu->id }}"></td>
                                        <td class="fw-semibold">{{ $menu->title }}</td>
                                        <td><code>{{ $menu->slug }}</code></td>
                                        <td>{{ $menu->pages_count }}</td>
                                        <td>
                                            <span class="badge {{ $menu->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                                {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.nav-menu.edit', $menu) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('admin.nav-menu.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit" @disabled($menu->pages_count > 0)>Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada menu dinamis.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if ($menus->isNotEmpty())
                <button type="submit" class="btn btn-light-brand mt-3">Simpan Urutan</button>
            @endif
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('nav-menu-sortable');
    if (!tbody || typeof Sortable === 'undefined') return;
    Sortable.create(tbody, {
        handle: 'td',
        animation: 150,
        onEnd: function () {
            tbody.querySelectorAll('tr[data-id]').forEach(function (row) {
                const input = row.querySelector('input[name="order[]"]');
                if (input) input.value = row.getAttribute('data-id');
            });
        }
    });
});
</script>
@endpush
