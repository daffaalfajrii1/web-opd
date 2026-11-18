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
                                <form action="{{ route('admin.nav-menu.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" @disabled($menu->pages_count > 0)>Hapus</button>
                                </form>
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
@endsection
