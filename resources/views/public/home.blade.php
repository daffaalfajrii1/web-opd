@extends('public.layouts.clinicom')

@section('title', 'Beranda')

@section('content')
    @include('public.partials.clinicom.carousel')

    <div class="container">
        <div class="rsud-doctor-search">
            <form method="get" action="{{ route('public.dokter.index') }}" class="row">
                <div class="col-md-8 col-sm-12" style="margin-bottom:.75rem">
                    <input type="search" name="q" class="form-control" placeholder="Cari dokter / spesialisasi..." value="{{ request('q') }}">
                </div>
                <div class="col-md-4 col-sm-12">
                    <button type="submit" class="btn btn-gradient btn-md btn-block">Cari Dokter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="about-area default-padding">
        <div class="container">
            <div class="row align-center">
                <div class="col-lg-6">
                    <h2>{{ $websiteSettings->siteName() }}</h2>
                    <p>{{ $websiteSettings->site_tagline ?: 'Sumber informasi, layanan, dan transparansi '.$websiteSettings->siteName().'.' }}</p>
                    <a class="btn btn-md btn-gradient" href="{{ route('public.layanan.index') }}">Layanan Online</a>
                    <a class="btn btn-md btn-light border" href="{{ route('public.dokter.jadwal') }}">Jadwal Dokter</a>
                </div>
                <div class="col-lg-6">
                    <div class="row text-center">
                        <div class="col-6" style="margin-bottom:1rem">
                            <h2 class="text-primary">{{ $totalPegawai }}</h2>
                            <p>Pegawai</p>
                        </div>
                        <div class="col-6" style="margin-bottom:1rem">
                            <h2 class="text-primary">{{ $layanans->count() }}</h2>
                            <p>Layanan Aktif</p>
                        </div>
                        <div class="col-6">
                            <h2 class="text-primary">{{ $featuredDokters->count() ?? 0 }}</h2>
                            <p>Dokter</p>
                        </div>
                        <div class="col-6">
                            <h2 class="text-primary">{{ $totalJabatan }}</h2>
                            <p>Jabatan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (($featuredDokters ?? collect())->isNotEmpty())
        <div class="doctors-area three-colum bg-gray default-padding bottom-less">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="site-heading text-center">
                            <h2>Dokter Kami</h2>
                            <p>Klik kartu dokter untuk melihat jadwal praktik.</p>
                        </div>
                    </div>
                </div>
                <div class="doctor-items text-center">
                    <div class="row">
                        @foreach ($featuredDokters as $dokter)
                            <div class="col-lg-4 col-md-6 single-item rsud-doctor-card">
                                <div class="item">
                                    <div class="thumb">
                                        <a href="{{ route('public.dokter.show', $dokter->slug) }}">
                                            <img src="{{ $dokter->photoUrl() }}" alt="{{ $dokter->name }}">
                                        </a>
                                        <div class="button">
                                            <a href="{{ route('public.dokter.show', $dokter->slug) }}">Lihat Jadwal</a>
                                        </div>
                                    </div>
                                    <div class="info">
                                        <h4><a href="{{ route('public.dokter.show', $dokter->slug) }}">{{ $dokter->name }}</a></h4>
                                        <span>{{ $dokter->specialty }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="text-center" style="padding:2rem 0 1rem">
                    <a class="btn btn-md btn-gradient" href="{{ route('public.dokter.index') }}">Semua Dokter</a>
                </div>
            </div>
        </div>
    @endif

    @if ($latestBeritas->isNotEmpty())
        <div class="blog-area default-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="site-heading text-center">
                            <h2>Berita Terbaru</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach ($latestBeritas as $berita)
                        <div class="col-lg-4 col-md-6" style="margin-bottom:1.5rem">
                            <div class="item" style="background:#fff;padding:1rem;border-radius:8px;height:100%">
                                <h4><a href="{{ route('public.berita.show', $berita->slug) }}">{{ $berita->title }}</a></h4>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($berita->excerpt ?: $berita->content), 120) }}</p>
                                <a href="{{ route('public.berita.show', $berita->slug) }}">Baca selengkapnya</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
