@extends('admin.layouts.app')

@section('title', 'Tambah Dokter')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Dokter',
        'crumbs' => [
            ['label' => 'Dokter', 'url' => route('admin.dokter.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.dokter.store') }}" enctype="multipart/form-data" id="dokter-form">
                    @csrf
                    @include('admin.dokter._form')
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.dokter.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection