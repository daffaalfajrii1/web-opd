@extends('admin.layouts.app')

@section('title', 'Menu Navbar')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Menu Navbar Dinamis',
        'crumbs' => [['label' => 'Menu Navbar']],
        'actions' => '<a href="'.route('admin.nav-menu.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Menu</a>',
    ])

    <div class="main-content">
        <div class="alert alert-light border mb-4">
            Menu utama tampil berjajar di navbar. Urutan sub-menu (halaman dinamis) diatur di dalam masing-masing menu lewat drag.
            Menghapus menu akan menghapus seluruh sub-menunya juga.
        </div>

        <div class="row g-3">
            @forelse ($menus as $menu)
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <h5 class="mb-1">{{ $menu->title }}</h5>
                                    <code class="fs-12">{{ $menu->slug }}</code>
                                </div>
                                <span class="badge {{ $menu->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                    {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <p class="text-muted mb-3">{{ $menu->pages_count }} sub-menu / halaman</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.nav-menu.edit', $menu) }}" class="btn btn-sm btn-primary">Kelola Sub-menu</a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteNavMenuModal"
                                    data-action="{{ route('admin.nav-menu.destroy', $menu) }}"
                                    data-title="{{ $menu->title }}"
                                    data-count="{{ $menu->pages_count }}"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center text-muted py-5">Belum ada menu dinamis.</div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="deleteNavMenuModal" tabindex="-1" aria-labelledby="deleteNavMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNavMenuModalLabel">Hapus Menu Navbar?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Anda akan menghapus menu <strong id="deleteNavMenuTitle">—</strong>.
                    </p>
                    <p class="mb-0 text-danger" id="deleteNavMenuWarn">
                        Semua sub-menu di dalamnya juga akan dihapus.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteNavMenuForm" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('deleteNavMenuModal')?.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const action = button.getAttribute('data-action');
        const title = button.getAttribute('data-title') || 'menu ini';
        const count = Number(button.getAttribute('data-count') || 0);

        document.getElementById('deleteNavMenuForm').setAttribute('action', action);
        document.getElementById('deleteNavMenuTitle').textContent = title;

        const warn = document.getElementById('deleteNavMenuWarn');
        if (count > 0) {
            warn.textContent = `Menu ini punya ${count} sub-menu. Semua sub-menu tersebut juga akan ikut terhapus.`;
            warn.classList.remove('d-none');
        } else {
            warn.textContent = 'Menu ini tidak punya sub-menu.';
            warn.classList.remove('text-danger');
        }
    });
</script>
@endpush
