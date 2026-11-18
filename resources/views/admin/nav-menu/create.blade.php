@extends('admin.layouts.app')
@section('title', 'Tambah Menu Navbar')
@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Menu Navbar',
        'crumbs' => [
            ['label' => 'Menu Navbar', 'url' => route('admin.nav-menu.index')],
            ['label' => 'Tambah'],
        ],
    ])
    <div class="main-content">
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('admin.nav-menu.store') }}">
                @csrf
                @include('admin.nav-menu._form')
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('admin.nav-menu.index') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div></div>
    </div>
@endsection
