@extends('public.layouts.optilux')

@section('title', 'Beranda')

@section('content')
    @include('public.partials.optilux.carousel')

    <section class="kecamatan-hero">
        <div class="container">
            <div class="row g-4 align-items-center py-5">
                <div class="col-lg-7">
                    <span class="kecamatan-hero__badge wow fadeInUp">Portal Resmi {{ $websiteSettings->shortName() }}</span>
                    <h1 class="kecamatan-hero__title wow fadeInUp" data-wow-delay=".1s">{{ $websiteSettings->siteName() }}</h1>
                    <p class="kecamatan-hero__lead wow fadeInUp" data-wow-delay=".2s">
                        {{ $websiteSettings->site_tagline ?: 'Sumber informasi, layanan, dan transparansi '.$websiteSettings->siteName().'.' }}
                    </p>
                    <div class="kecamatan-hero__actions wow fadeInUp" data-wow-delay=".3s">
                        <a class="btn-main fx-slide" href="{{ route('public.layanan.index') }}"><span>Layanan Online</span></a>
                        @if ($profilPages->isNotEmpty())
                            <a class="kecamatan-hero__btn-alt" href="{{ route('public.profil.show', $profilPages->first()->slug) }}">{{ $profilPages->first()->title }}</a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="kecamatan-hero-stats wow fadeInRight" data-wow-delay=".2s">
                        <div class="kecamatan-hero-stats__title">Ringkasan {{ $websiteSettings->shortName() }}</div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="kecamatan-hero-stats__item">
                                    <div class="kecamatan-hero-stats__number">{{ $totalPegawai }}</div>
                                    <div class="kecamatan-hero-stats__label">Pegawai</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="kecamatan-hero-stats__item">
                                    <div class="kecamatan-hero-stats__number">{{ $layanans->count() }}</div>
                                    <div class="kecamatan-hero-stats__label">Layanan Aktif</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($latestBeritas->isNotEmpty())
        <section>
            <div class="container">
                @include('public.partials.optilux.section-heading', [
                    'subtitle' => 'Berita Terbaru',
                    'title' => 'Informasi & Kegiatan '.$websiteSettings->shortName(),
                    'buttonUrl' => route('public.berita.index'),
                    'buttonLabel' => 'Semua Berita',
                ])
                <div class="row g-4">
                    @foreach ($latestBeritas as $berita)
                        <div class="col-lg-4 col-md-6">
                            @include('public.partials.optilux.berita-card', ['berita' => $berita])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($upcomingAgendas->isNotEmpty())
        <section class="bg-color-op-2">
            <div class="container">
                @include('public.partials.optilux.section-heading', [
                    'subtitle' => 'Agenda',
                    'title' => 'Kegiatan Mendatang',
                    'buttonUrl' => route('public.agenda.index'),
                    'buttonLabel' => 'Semua Agenda',
                ])
                <div class="row g-4">
                    @foreach ($upcomingAgendas as $agenda)
                        <div class="col-lg-4">
                            <div class="bg-white p-4 rounded-1 h-100 shadow-soft">
                                <div class="fw-bold id-color mb-2">{{ $agenda->formattedEventDate() }}</div>
                                <h4 class="mb-2"><a href="{{ route('public.agenda.show', $agenda->slug) }}" class="text-dark">{{ $agenda->title }}</a></h4>
                                <p class="mb-2"><i class="icofont-location-pin id-color me-1"></i>{{ $agenda->locationDisplay() }}</p>
                                <p class="mb-0">{{ $agenda->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($agenda->content), 100) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($layanans->isNotEmpty())
        <section>
            <div class="container">
                @include('public.partials.optilux.section-heading', [
                    'subtitle' => 'Layanan Publik',
                    'title' => 'Pelayanan Masyarakat',
                    'buttonUrl' => route('public.layanan.index'),
                    'buttonLabel' => 'Semua Layanan',
                ])
                <div class="row g-4">
                    @foreach ($layanans as $index => $layanan)
                        <div class="col-lg-4 col-md-6">
                            @include('public.partials.optilux.layanan-card', ['layanan' => $layanan, 'index' => $index])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($latestPengumumans->isNotEmpty())
        <section class="bg-color-op-2">
            <div class="container">
                @include('public.partials.optilux.section-heading', [
                    'subtitle' => 'Pengumuman',
                    'title' => 'Informasi Penting',
                    'buttonUrl' => route('public.pengumuman.index'),
                    'buttonLabel' => 'Semua Pengumuman',
                ])
                <div class="row g-4">
                    @foreach ($latestPengumumans as $pengumuman)
                        <div class="col-lg-4">
                            <div class="bg-white p-4 rounded-1 h-100">
                                @if ($pengumuman->isPenting())
                                    <span class="kecamatan-badge-penting mb-2 d-inline-block">Penting</span>
                                @endif
                                <h4 class="mb-2"><a href="{{ route('public.pengumuman.show', $pengumuman->slug) }}" class="text-dark">{{ $pengumuman->title }}</a></h4>
                                <p class="mb-0">{{ $pengumuman->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($pengumuman->content), 120) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($featuredGaleris->isNotEmpty())
        <section>
            <div class="container">
                @include('public.partials.optilux.section-heading', [
                    'subtitle' => 'Galeri',
                    'title' => 'Dokumentasi Kegiatan',
                    'buttonUrl' => route('public.galeri.index'),
                    'buttonLabel' => 'Semua Galeri',
                ])
                <div class="row g-4">
                    @foreach ($featuredGaleris as $galeri)
                        <div class="col-lg-3 col-md-6">
                            @include('public.partials.optilux.galeri-album-card', [
                                'galeri' => $galeri,
                                'headingTag' => 'h5',
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($websiteSettings->mapsIframeSrc())
        <section class="bg-color-op-2">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-4">
                        <div class="subtitle">Lokasi</div>
                        <h2 class="kecamatan-section-title">Kantor {{ $websiteSettings->shortName() }}</h2>
                        @if ($websiteSettings->contact_address)
                            <p><i class="icofont-location-pin id-color me-2"></i>{{ $websiteSettings->contact_address }}</p>
                        @endif
                        @if ($websiteSettings->contact_phone)
                            <p><i class="icofont-phone id-color me-2"></i>{{ $websiteSettings->contact_phone }}</p>
                        @endif
                        <a href="{{ route('public.kontak.index') }}" class="btn-main fx-slide mt-2"><span>Hubungi Kami</span></a>
                    </div>
                    <div class="col-lg-8 kecamatan-map-embed">
                        <iframe
                            src="{{ $websiteSettings->mapsIframeSrc() }}"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Peta {{ $websiteSettings->siteName() }}"
                        ></iframe>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="bg-color relative text-light pt-50 pb-50">
        <div class="container relative z-2">
            <div class="row g-4 align-items-center">
                <div class="col-md-9">
                    <h3 class="mb-0 fs-32">Butuh layanan administrasi? Ajukan secara online sekarang.</h3>
                </div>
                <div class="col-lg-3 text-lg-end">
                    <a class="btn-main btn-line fx-slide" href="{{ route('public.layanan.check-status') }}"><span>Cek Status</span></a>
                </div>
            </div>
        </div>
    </section>
@endsection
