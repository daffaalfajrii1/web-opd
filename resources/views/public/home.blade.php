@extends('public.layouts.clinicom')

@section('title', 'Beranda')

@section('content')
    @include('public.partials.clinicom.carousel')

    <section class="rsud-block rsud-block--search">
        <div class="container">
            <div class="rsud-doctor-search">
                <form method="get" action="{{ route('public.dokter.index') }}" class="row align-items-center">
                    <div class="col-md-8 col-sm-12 mb-2 mb-md-0">
                        <input type="search" name="q" class="form-control" placeholder="Cari dokter / spesialisasi..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <button type="submit" class="btn btn-gradient btn-md btn-block">Cari Dokter</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="rsud-block rsud-block--portal">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="site-heading text-left rsud-portal-heading">
                        <h4>Portal Resmi</h4>
                        <h2>{{ $websiteSettings->siteName() }}</h2>
                    </div>
                    <p class="rsud-lead">{{ $websiteSettings->site_tagline ?: 'Sumber informasi, layanan, dan transparansi '.$websiteSettings->siteName().'.' }}</p>
                    <div class="rsud-actions">
                        <a class="btn btn-md btn-gradient" href="{{ route('public.layanan.index') }}">Layanan Online</a>
                        <a class="btn btn-md btn-border light" href="{{ route('public.dokter.jadwal') }}">Jadwal Dokter</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row text-center rsud-stats">
                        <div class="col-6">
                            <div class="rsud-stat-card">
                                <h2>{{ $totalPegawai }}</h2>
                                <p>Pegawai</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rsud-stat-card">
                                <h2>{{ $totalDokter }}</h2>
                                <p>Dokter</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rsud-block rsud-block--muted">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="site-heading text-center">
                        <h4>Update Terkini</h4>
                        <h2>Berita Terbaru</h2>
                    </div>
                </div>
            </div>
            <div class="row rsud-gap justify-content-center">
                @forelse ($latestBeritas as $berita)
                    <div class="col-lg-4 col-md-6">
                        @include('public.partials.clinicom.berita-card', ['berita' => $berita])
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted mb-0">Belum ada berita terbaru.</p>
                    </div>
                @endforelse
            </div>
            @if ($latestBeritas->isNotEmpty())
                <div class="text-center rsud-block__cta">
                    <a class="btn btn-md btn-gradient" href="{{ route('public.berita.index') }}">Semua Berita</a>
                </div>
            @endif
        </div>
    </section>

    <section class="rsud-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="site-heading text-center">
                        <h4>Publikasi</h4>
                        <h2>Pengumuman</h2>
                    </div>
                </div>
            </div>
            <div class="row rsud-gap justify-content-center">
                @forelse ($latestPengumumans as $pengumuman)
                    <div class="col-lg-4 col-md-6">
                        <article class="rsud-card">
                            @if ($pengumuman->is_featured)
                                <span class="rsud-card__badge">Unggulan</span>
                            @endif
                            <small class="rsud-card__meta">{{ optional($pengumuman->published_at)->translatedFormat('d F Y') }}</small>
                            <h4><a href="{{ route('public.pengumuman.show', $pengumuman->slug) }}">{{ $pengumuman->title }}</a></h4>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($pengumuman->excerpt ?: $pengumuman->content), 110) }}</p>
                            <a class="rsud-card__link" href="{{ route('public.pengumuman.show', $pengumuman->slug) }}">Baca pengumuman</a>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted mb-0">Belum ada pengumuman aktif.</p>
                    </div>
                @endforelse
            </div>
            @if ($latestPengumumans->isNotEmpty())
                <div class="text-center rsud-block__cta">
                    <a class="btn btn-md btn-gradient" href="{{ route('public.pengumuman.index') }}">Semua Pengumuman</a>
                </div>
            @endif
        </div>
    </section>

    <section class="rsud-block rsud-block--muted">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="site-heading text-center">
                        <h4>Kalender</h4>
                        <h2>Agenda Mendatang</h2>
                    </div>
                </div>
            </div>
            <div class="row rsud-gap justify-content-center">
                @forelse ($upcomingAgendas as $agenda)
                    <div class="col-lg-4 col-md-6">
                        <article class="rsud-card">
                            <small class="rsud-card__meta">{{ $agenda->formattedEventDate() }}</small>
                            <h4><a href="{{ route('public.agenda.show', $agenda->slug) }}">{{ $agenda->title }}</a></h4>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($agenda->excerpt ?: $agenda->description ?? ''), 100) ?: ($agenda->locationDisplay() ?? 'Agenda kegiatan') }}</p>
                            <a class="rsud-card__link" href="{{ route('public.agenda.show', $agenda->slug) }}">Detail agenda</a>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted mb-0">Belum ada agenda mendatang.</p>
                    </div>
                @endforelse
            </div>
            @if ($upcomingAgendas->isNotEmpty())
                <div class="text-center rsud-block__cta">
                    <a class="btn btn-md btn-gradient" href="{{ route('public.agenda.index') }}">Semua Agenda</a>
                </div>
            @endif
        </div>
    </section>

    <section class="rsud-block doctors-area three-colum bottom-less">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="site-heading text-center">
                        <h4>Tenaga Medis</h4>
                        <h2>Dokter Kami</h2>
                        <p>Klik kartu dokter untuk melihat jadwal praktik.</p>
                    </div>
                </div>
            </div>
            <div class="doctor-items text-center">
                <div class="row rsud-gap justify-content-center">
                    @forelse ($featuredDokters as $dokter)
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
                                    <span>{{ $dokter->specialtyLabel() }}</span>
                                    @if ($dokter->poli)
                                        <small class="d-block text-muted mt-1">{{ $dokter->poli }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted mb-0">Belum ada data dokter.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @if ($featuredDokters->isNotEmpty())
                <div class="text-center rsud-block__cta">
                    <a class="btn btn-md btn-gradient" href="{{ route('public.dokter.index') }}">Semua Dokter</a>
                </div>
            @endif
        </div>
    </section>

    @if ($layanans->isNotEmpty())
        <section class="rsud-block rsud-block--muted">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <div class="site-heading text-center">
                            <h4>Pelayanan</h4>
                            <h2>Layanan Online</h2>
                        </div>
                    </div>
                </div>
                <div class="row rsud-gap justify-content-center">
                    @foreach ($layanans as $layanan)
                        <div class="col-lg-4 col-md-6">
                            <article class="rsud-card">
                                <h4><a href="{{ route('public.layanan.show', $layanan->slug) }}">{{ $layanan->title }}</a></h4>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($layanan->description ?? ''), 110) ?: 'Ajukan layanan secara online.' }}</p>
                                <a class="rsud-card__link" href="{{ route('public.layanan.show', $layanan->slug) }}">Ajukan sekarang</a>
                            </article>
                        </div>
                    @endforeach
                </div>
                <div class="text-center rsud-block__cta">
                    <a class="btn btn-md btn-gradient" href="{{ route('public.layanan.index') }}">Semua Layanan</a>
                </div>
            </div>
        </section>
    @endif
@endsection
