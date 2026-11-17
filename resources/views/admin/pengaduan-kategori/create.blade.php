@extends('admin.layouts.app')

@section('title', 'Tambah Kategori Pengaduan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Kategori',
        'crumbs' => [
            ['label' => 'Pengaduan', 'url' => route('admin.pengaduan.index')],
            ['label' => 'Kategori', 'url' => route('admin.pengaduan-kategori.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pengaduan-kategori.store') }}">
                    @csrf
                    @include('admin.pengaduan-kategori._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.pengaduan-kategori.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
