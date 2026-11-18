@extends('public.layouts.clinicom')

@section('title', 'Dokter')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Daftar Dokter',
        'breadcrumbs' => [['label' => 'Dokter']],
    ])

    <div class="doctors-area three-colum bg-gray default-padding bottom-less">
        <div class="container">
            <form method="GET" class="row g-2 justify-content-center mb-5">
                <div class="col-md-6">
                    <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama atau spesialisasi dokter...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-gradient w-100">Cari</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('public.dokter.jadwal') }}" class="btn btn-light w-100">Jadwal Dokter</a>
                </div>
            </form>

            <div class="doctor-items text-center">
                <div class="row">
                    @forelse($dokters as $dokter)
                        <div class="col-lg-4 col-md-6 single-item mb-4">
                            <div class="item">
                                <div class="thumb">
                                    <a href="{{ route('public.dokter.show', $dokter->slug) }}">
                                        @if ($dokter->photoUrl())
                                            <img src="{{ $dokter->photoUrl() }}" alt="{{ $dokter->name }}">
                                        @else
                                            <img src="{{ asset('clinicom/assets/img/doctor.png') }}" alt="{{ $dokter->name }}">
                                        @endif
                                    </a>
                                    <div class="button">
                                        <a href="{{ route('public.dokter.show', $dokter->slug) }}">Detail Dokter</a>
                                    </div>
                                </div>
                                <div class="info">
                                    <h4><a href="{{ route('public.dokter.show', $dokter->slug) }}">{{ $dokter->name }}</a></h4>
                                    <span>{{ $dokter->specialty }}</span>
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