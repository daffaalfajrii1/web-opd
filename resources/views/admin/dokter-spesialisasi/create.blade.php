@extends('admin.layouts.app')
@section('title', 'Tambah Spesialisasi')
@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Spesialisasi',
        'crumbs' => [
            ['label' => 'Dokter', 'url' => route('admin.dokter.index')],
            ['label' => 'Spesialisasi', 'url' => route('admin.dokter-spesialisasi.index')],
            ['label' => 'Tambah'],
        ],
    ])
    <div class="main-content">
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('admin.dokter-spesialisasi.store') }}">
                @csrf
                @include('admin.dokter-spesialisasi._form')
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('admin.dokter-spesialisasi.index') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div></div>
    </div>
@endsection
