@extends('admin.layouts.app')

@section('title', 'Edit Link Layanan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Link Layanan',
        'crumbs' => [
            ['label' => 'Link Layanan', 'url' => route('admin.link-layanan.index')],
            ['label' => 'Edit'],
        ],
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.link-layanan.update', $link) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.link-layanan._form', ['link' => $link])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.link-layanan.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
