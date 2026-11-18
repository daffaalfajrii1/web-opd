@extends('admin.layouts.app')
@section('title', 'Edit Spesialisasi')
@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Spesialisasi',
        'crumbs' => [
            ['label' => 'Dokter', 'url' => route('admin.dokter.index')],
            ['label' => 'Spesialisasi', 'url' => route('admin.dokter-spesialisasi.index')],
            ['label' => 'Edit'],
        ],
    ])
    <div class="main-content">
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('admin.dokter-spesialisasi.update', $spesialisasi) }}">
                @csrf
                @method('PUT')
                @include('admin.dokter-spesialisasi._form', ['spesialisasi' => $spesialisasi])
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                    <a href="{{ route('admin.dokter-spesialisasi.index') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div></div>
    </div>
@endsection
