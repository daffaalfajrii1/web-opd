@extends('public.layouts.clinicom')

@section('title', 'Dokter')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Daftar Dokter',
        'breadcrumbs' => [['label' => 'Dokter']],
    ])

    <div class="doctors-area three-colum bg-gray default-padding bottom-less rsud-content-block">
        <div class="container">
            <form method="GET" class="row justify-content-center mb-5">
                <div class="col-md-6 mb-2">
                    <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama atau spesialisasi dokter...">
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-gradient btn-block">Cari</button>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('public.dokter.jadwal') }}" class="btn btn-light btn-block border">Jadwal Dokter</a>
                </div>
            </form>

            <div class="doctor-items text-center">
                <div class="row justify-content-center">
                    @forelse($dokters as $dokter)
                        <div class="col-lg-4 col-md-6 single-item mb-4 rsud-doctor-card">
                            <div class="item">
                                <div class="thumb">
                                    <a href="{{ route('public.dokter.show', $dokter->slug) }}">
                                        <img src="{{ $dokter->photoUrl() }}" alt="{{ $dokter->name }}">
                                    </a>
                                    <div class="button">
                                        <a href="{{ route('public.dokter.show', $dokter->slug) }}">Detail Dokter</a>
                                    </div>
                                </div>
                                <div class="info">
                                    <h4><a href="{{ route('public.dokter.show', $dokter->slug) }}">{{ $dokter->name }}</a></h4>
                                    <span>{{ $dokter->specialtyLabel() }}</span>
                                    @if ($dokter->poli)
                                        <small class="d-block text-muted mt-1">{{ $dokter->poli }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted mb-0">Tidak ada dokter yang ditemukan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if ($dokters->hasPages())
                <div class="mt-4 text-center">{{ $dokters->links() }}</div>
            @endif
        </div>
    </div>
@endsection