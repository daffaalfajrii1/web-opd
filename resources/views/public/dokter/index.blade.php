@extends('public.layouts.clinicom')

@section('title', 'Dokter')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Daftar Dokter',
        'breadcrumbs' => [['label' => 'Dokter']],
    ])

    <section class="rsud-strip rsud-strip--soft">
        <div class="container">
            <form method="GET" class="rsud-filter-bar row align-items-stretch mb-4">
                <div class="col-md-6 mb-2 mb-md-0">
                    <input type="search" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari nama atau spesialisasi dokter...">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <button type="submit" class="btn btn-gradient btn-block">Cari</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('public.dokter.jadwal') }}" class="btn rsud-btn-outline btn-block">Jadwal Dokter</a>
                </div>
            </form>

            <div class="row justify-content-center">
                @forelse($dokters as $dokter)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <a href="{{ route('public.dokter.show', $dokter->slug) }}" class="rsud-doc">
                            <img src="{{ $dokter->photoUrl() }}" alt="{{ $dokter->name }}">
                            <h3>{{ $dokter->name }}</h3>
                            <span>{{ $dokter->specialtyLabel() }}</span>
                            @if ($dokter->poli)
                                <small class="d-block text-muted mt-1">{{ $dokter->poli }}</small>
                            @endif
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted mb-0">Tidak ada dokter yang ditemukan.</p>
                    </div>
                @endforelse
            </div>

            @if ($dokters->hasPages())
                <div class="mt-2 text-center">{{ $dokters->links() }}</div>
            @endif
        </div>
    </section>
@endsection
