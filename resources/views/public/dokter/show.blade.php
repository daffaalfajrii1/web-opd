@extends('public.layouts.clinicom')

@section('title', $dokter->name)

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => $dokter->name,
        'breadcrumbs' => [
            ['label' => 'Dokter', 'url' => route('public.dokter.index')],
            ['label' => $dokter->name],
        ],
    ])

    <div class="doctor-single-area text-center overflow-hidden default-padding rsud-content-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="top-info">
                        <ul>
                            <li>
                                <img src="{{ $dokter->photoUrl() }}" alt="{{ $dokter->name }}">
                            </li>
                            <li>
                                <h4>{{ $dokter->name }}</h4>
                                <span>{{ $dokter->specialtyLabel() }}</span>
                                @if ($dokter->poli)
                                    <small class="d-block text-muted mt-1">{{ $dokter->poli }}</small>
                                @endif
                            </li>
                        </ul>
                    </div>

                    <ul class="list-inline rsud-doctor-contacts mb-4">
                        @if ($dokter->phone)
                            <li class="list-inline-item mx-2 my-1">
                                <a href="tel:{{ preg_replace('/\s+/', '', $dokter->phone) }}"><i class="fas fa-phone"></i> {{ $dokter->phone }}</a>
                            </li>
                        @endif
                        @if ($dokter->email)
                            <li class="list-inline-item mx-2 my-1">
                                <a href="mailto:{{ $dokter->email }}"><i class="fas fa-envelope"></i> {{ $dokter->email }}</a>
                            </li>
                        @endif
                        @if ($dokter->facebook_url)
                            <li class="list-inline-item mx-2 my-1">
                                <a href="{{ $dokter->facebook_url }}" target="_blank" rel="noopener"><i class="fab fa-facebook"></i> Facebook</a>
                            </li>
                        @endif
                    </ul>

                    @if ($dokter->bio)
                        <div class="bottom-info text-left">
                            <p>{!! nl2br(e($dokter->bio)) !!}</p>
                        </div>
                    @endif

                    <div class="mt-5 text-left">
                        <h3 class="text-center mb-4">Jadwal Praktik</h3>
                        @if ($dokter->jadwals->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-bordered rsud-table">
                                    <thead>
                                        <tr>
                                            <th>Hari</th>
                                            <th>Jam</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dokter->jadwals as $jadwal)
                                            <tr>
                                                <td>{{ $jadwal->dayLabel() }}</td>
                                                <td>{{ $jadwal->formatTimeRange() }}</td>
                                                <td>{{ $jadwal->note ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted mb-0">Jadwal praktik belum tersedia.</p>
                        @endif
                        <div class="text-center mt-4">
                            <a class="btn btn-md btn-gradient" href="{{ route('public.dokter.jadwal') }}">Lihat Semua Jadwal <i class="fas fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
