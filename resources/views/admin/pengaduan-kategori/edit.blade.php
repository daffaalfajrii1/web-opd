@extends('admin.layouts.app')

@section('title', 'Edit Kategori Pengaduan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Kategori',
        'crumbs' => [
            ['label' => 'Pengaduan', 'url' => route('admin.pengaduan.index')],
            ['label' => 'Kategori', 'url' => route('admin.pengaduan-kategori.index')],
            ['label' => 'Edit'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pengaduan-kategori.update', $category) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.pengaduan-kategori._form', ['category' => $category])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.pengaduan-kategori.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
