@extends('admin.layouts.app')

@section('title', 'Tambah Link Layanan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Link Layanan',
        'crumbs' => [
            ['label' => 'Link Layanan', 'url' => route('admin.link-layanan.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.link-layanan.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('admin.link-layanan._form')
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.link-layanan.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
