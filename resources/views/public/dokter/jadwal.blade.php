@extends('public.layouts.clinicom')

@section('title', 'Jadwal Dokter')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Jadwal Dokter',
        'breadcrumbs' => [
            ['label' => 'Dokter', 'url' => route('public.dokter.index')],
            ['label' => 'Jadwal'],
        ],
    ])

    <div class="default-padding bg-gray bottom-less">
        <div class="container">
            <form method="GET" class="row g-2 mb-5">
                <div class="col-md-5">
                    <select name="dokter_id" class="form-control">
                        <option value="">Semua dokter</option>
                        @foreach ($dokters as $dokterOption)
                            <option value="{{ $dokterOption->id }}" @selected(request('dokter_id') == $dokterOption->id)>{{ $dokterOption->name }} — {{ $dokterOption->specialtyLabel() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="day" class="form-control">
                        <option value="">Semua hari</option>
                        @foreach ($dayOptions as $dayValue => $dayLabel)
                            <option value="{{ $dayValue }}" @selected((int) request('day') === $dayValue)>{{ $dayLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-gradient w-100">Filter</button>
                </div>
            </form>

            @forelse($groupedByDay as $dayOfWeek => $dayJadwals)
                <div class="mb-5">
                    <h4 class="mb-3">{{ $dayOptions[$dayOfWeek] ?? '—' }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered bg-white">
                            <thead>
                                <tr>
                                    <th>Dokter</th>
                                    <th>Spesialisasi</th>
                                    <th>Jam</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dayJadwals as $jadwal)
                                    <tr>
                                        <td>
                                            <a href="{{ route('public.dokter.show', $jadwal->dokter->slug) }}">{{ $jadwal->dokter->name }}</a>
                                        </td>
                                        <td>{{ $jadwal->dokter->specialtyLabel() }}</td>
                                        <td>{{ $jadwal->formatTimeRange() }}</td>
                                        <td>{{ $jadwal->note ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted mb-0">Tidak ada jadwal yang cocok dengan filter.</p>
            @endforelse
        </div>
    </div>
@endsection