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

    <div class="doctor-single-area text-center overflow-hidden default-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="top-info">
                        <ul>
                            <li>
                                @if ($dokter->photoUrl())
                                    <img src="{{ $dokter->photoUrl() }}" alt="{{ $dokter->name }}">
                                @else
                                    <img src="{{ asset('clinicom/assets/img/doctor.png') }}" alt="{{ $dokter->name }}">
                                @endif
                            </li>
                            <li>
                                <h4>{{ $dokter->name }}</h4>
                                <span>{{ $dokter->specialtyLabel() }}</span>
                            </li>
                        </ul>
                    </div>
                    @if ($dokter->bio)
                        <div class="bottom-info text-left">
                            <p>{!! nl2br(e($dokter->bio)) !!}</p>
                        </div>
                    @endif

                    <div class="mt-5 text-left">
                        <h3 class="text-center mb-4">Jadwal Praktik</h3>
                        @if ($dokter->jadwals->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-bordered bg-white">
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