@extends('public.layouts.clinicom')

@section('title', 'Beranda')

@section('content')
    @include('public.partials.clinicom.carousel')

    {{-- Strip cari dokter --}}
    <section class="rsud-strip rsud-strip--teal">
        <div class="container">
            <form method="get" action="{{ route('public.dokter.index') }}" class="rsud-search-bar row align-items-center">
                <div class="col-md-8 mb-2 mb-md-0">
                    <input type="search" name="q" class="form-control" placeholder="Cari dokter / spesialisasi..." value="{{ request('q') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-light btn-block rsud-btn-solid-light">Cari Dokter</button>
                </div>
            </form>
        </div>
    </section>

    {{-- Portal resmi --}}
    <section class="rsud-strip rsud-strip--white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <p class="rsud-kicker">Portal Resmi</p>
                    <h2 class="rsud-title">{{ $websiteSettings->siteName() }}</h2>
                    <p class="rsud-lead">{{ $websiteSettings->site_tagline ?: 'Sumber informasi, layanan, dan transparansi '.$websiteSettings->siteName().'.' }}</p>
                    <div class="rsud-actions">
                        <a class="btn btn-gradient" href="{{ route('public.layanan.index') }}">Layanan Online</a>
                        <a class="btn rsud-btn-outline" href="{{ route('public.dokter.jadwal') }}">Jadwal Dokter</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row">
                        <div class="col-6">
                            <div class="rsud-stat">
                                <strong>{{ $totalPegawai }}</strong>
                                <span>Pegawai</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rsud-stat">
                                <strong>{{ $totalDokter }}</strong>
                                <span>Dokter</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Berita: section gelap seperti referensi --}}
    <section class="rsud-strip rsud-strip--dark rsud-strip--news">
        <div class="container">
            <div class="rsud-strip__head">
                <div>
                    <p class="rsud-kicker rsud-kicker--light">Update Terkini</p>
                    <h2 class="rsud-title rsud-title--light">Berita Terbaru</h2>
                </div>
                <a class="btn rsud-btn-outline-light" href="{{ route('public.berita.index') }}">Semua Berita</a>
            </div>
            <div class="row">
                @forelse ($latestBeritas as $berita)
                    <div class="col-lg-3 col-md-6 mb-4">
                        @include('public.partials.clinicom.berita-card', ['berita' => $berita])
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-white-50 mb-0">Belum ada berita terbaru.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Pengumuman --}}
    <section class="rsud-strip rsud-strip--soft">
        <div class="container">
            <div class="rsud-strip__head">
                <div>
                    <p class="rsud-kicker">Publikasi</p>
                    <h2 class="rsud-title">Pengumuman</h2>
                </div>
                <a class="btn rsud-btn-outline" href="{{ route('public.pengumuman.index') }}">Semua Pengumuman</a>
            </div>
            <div class="row">
                @forelse ($latestPengumumans as $pengumuman)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <article class="rsud-panel">
                            @if ($pengumuman->is_featured)
                                <span class="rsud-panel__tag">Unggulan</span>
                            @endif
                            <small>{{ optional($pengumuman->published_at)->translatedFormat('d F Y') }}</small>
                            <h3><a href="{{ route('public.pengumuman.show', $pengumuman->slug) }}">{{ $pengumuman->title }}</a></h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($pengumuman->excerpt ?: $pengumuman->content), 100) }}</p>
                            <a class="rsud-panel__link" href="{{ route('public.pengumuman.show', $pengumuman->slug) }}">Baca pengumuman</a>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><p class="text-muted mb-0 text-center">Belum ada pengumuman aktif.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Agenda --}}
    <section class="rsud-strip rsud-strip--white">
        <div class="container">
            <div class="rsud-strip__head">
                <div>
                    <p class="rsud-kicker">Kalender</p>
                    <h2 class="rsud-title">Agenda Mendatang</h2>
                </div>
                <a class="btn rsud-btn-outline" href="{{ route('public.agenda.index') }}">Semua Agenda</a>
            </div>
            <div class="row">
                @forelse ($upcomingAgendas as $agenda)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <article class="rsud-agenda">
                            <div class="rsud-agenda__date">{{ $agenda->formattedEventDate() }}</div>
                            <h3><a href="{{ route('public.agenda.show', $agenda->slug) }}">{{ $agenda->title }}</a></h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($agenda->excerpt ?: $agenda->description ?? ''), 90) ?: ($agenda->locationDisplay() ?? 'Agenda kegiatan') }}</p>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><p class="text-muted mb-0 text-center">Belum ada agenda mendatang.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Dokter --}}
    <section class="rsud-strip rsud-strip--mint">
        <div class="container">
            <div class="rsud-strip__head">
                <div>
                    <p class="rsud-kicker">Tenaga Medis</p>
                    <h2 class="rsud-title">Dokter Kami</h2>
                </div>
                <a class="btn rsud-btn-outline" href="{{ route('public.dokter.index') }}">Semua Dokter</a>
            </div>
            <div class="row justify-content-center">
                @forelse ($featuredDokters as $dokter)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <a href="{{ route('public.dokter.show', $dokter->slug) }}" class="rsud-doc">
                            <img src="{{ $dokter->photoUrl() }}" alt="{{ $dokter->name }}">
                            <h3>{{ $dokter->name }}</h3>
                            <span>{{ $dokter->specialtyLabel() }}</span>
                        </a>
                    </div>
                @empty
                    <div class="col-12"><p class="text-muted mb-0 text-center">Belum ada data dokter.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    @if ($layanans->isNotEmpty())
        <section class="rsud-strip rsud-strip--white">
            <div class="container">
                <div class="rsud-strip__head">
                    <div>
                        <p class="rsud-kicker">Pelayanan</p>
                        <h2 class="rsud-title">Layanan Online</h2>
                    </div>
                    <a class="btn rsud-btn-outline" href="{{ route('public.layanan.index') }}">Semua Layanan</a>
                </div>
                <div class="row">
                    @foreach ($layanans as $layanan)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <article class="rsud-panel">
                                <h3><a href="{{ route('public.layanan.show', $layanan->slug) }}">{{ $layanan->title }}</a></h3>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($layanan->description ?? ''), 100) ?: 'Ajukan layanan secara online.' }}</p>
                                <a class="rsud-panel__link" href="{{ route('public.layanan.show', $layanan->slug) }}">Ajukan sekarang</a>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
