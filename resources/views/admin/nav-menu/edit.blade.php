@extends('admin.layouts.app')
@section('title', 'Edit Menu Navbar')
@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Menu: '.$menu->title,
        'crumbs' => [
            ['label' => 'Menu Navbar', 'url' => route('admin.nav-menu.index')],
            ['label' => 'Edit'],
        ],
    ])
    <div class="main-content">
        <div class="card mb-4"><div class="card-body">
            <form method="POST" action="{{ route('admin.nav-menu.update', $menu) }}">
                @csrf
                @method('PUT')
                @include('admin.nav-menu._form', ['menu' => $menu])
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                    <a href="{{ route('admin.nav-menu.index') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div></div>

        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Halaman di dropdown</h5>
                <a href="{{ route('admin.halaman.create', ['nav_menu_id' => $menu->id]) }}" class="btn btn-sm btn-primary">Tambah Halaman</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.nav-menu.pages.reorder', $menu) }}">
                    @csrf
                    <ul id="nav-page-sortable" class="list-group mb-3">
                        @forelse ($menu->pages as $page)
                            <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $page->id }}" style="cursor:grab">
                                <span>
                                    <i class="feather-menu me-2 text-muted"></i>
                                    {{ $page->title }}
                                    <input type="hidden" name="order[]" value="{{ $page->id }}">
                                </span>
                                <a href="{{ route('admin.halaman.edit', $page) }}" class="btn btn-sm btn-light">Edit</a>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Belum ada halaman. Tambah dari menu Halaman Profil dengan grup "Menu dinamis".</li>
                        @endforelse
                    </ul>
                    @if ($menu->pages->isNotEmpty())
                        <button type="submit" class="btn btn-light-brand">Simpan Urutan Dropdown</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('nav-page-sortable');
    if (!list || typeof Sortable === 'undefined') return;
    Sortable.create(list, {
        animation: 150,
        onEnd: function () {
            list.querySelectorAll('li[data-id]').forEach(function (row) {
                const input = row.querySelector('input[name="order[]"]');
                if (input) input.value = row.getAttribute('data-id');
            });
        }
    });
});
</script>
@endpush
