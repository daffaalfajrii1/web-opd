@extends('public.layouts.optilux')

@section('title', 'Pegawai')

@section('content')
    @include('public.partials.optilux.subheader', [
        'title' => 'Struktur Pegawai',
        'breadcrumbs' => [['label' => 'Pegawai']],
    ])

    <section>
        <div class="container">
            @if ($totalPegawai > 0)
                <p class="lead mb-5">Daftar aparatur dan pegawai {{ $websiteSettings->siteName() }}. Total {{ $totalPegawai }} pegawai.</p>
            @endif

            @if ($jabatanGroups->isNotEmpty())
                <div class="kecamatan-pegawai-grid">
                    @foreach ($jabatanGroups as $jabatan)
                        @foreach ($jabatan->pegawais as $pegawai)
                            <div class="kecamatan-pegawai-card">
                                <div class="kecamatan-pegawai-card__photo-wrap">
                                    @if ($pegawai->photoUrl())
                                        <img src="{{ $pegawai->photoUrl() }}" alt="{{ $pegawai->name }}" class="kecamatan-pegawai-card__photo">
                                    @else
                                        <div class="kecamatan-pegawai-card__fallback">{{ $pegawai->initials() }}</div>
                                    @endif
                                </div>
                                <div class="kecamatan-pegawai-card__body">
                                    <div class="kecamatan-pegawai-card__jabatan">{{ $jabatan->name }}</div>
                                    <div class="kecamatan-pegawai-card__name">{{ $pegawai->name }}</div>
                                    @if ($pegawai->nip)
                                        <div class="kecamatan-pegawai-card__meta">NIP {{ $pegawai->nip }}</div>
                                    @endif
                                    @if ($pegawai->bio)
                                        <p class="kecamatan-pegawai-card__bio">{{ \Illuminate\Support\Str::limit($pegawai->bio, 100) }}</p>
                                    @endif
                                    <div class="kecamatan-pegawai-card__contacts">
                                        @if ($pegawai->phone)
                                            <span><i class="icofont-phone id-color me-1"></i>{{ $pegawai->phone }}</span>
                                        @endif
                                        @if ($pegawai->email)
                                            <span><i class="icofont-envelope id-color me-1"></i>{{ $pegawai->email }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @else
                <div class="bg-light p-5 rounded-1 text-center">
                    <p class="mb-0">Belum ada data pegawai yang dipublikasikan.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
