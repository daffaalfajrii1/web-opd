@php
    $logoUrl = $websiteSettings->logoUrl() ?? asset('clinicom/assets/img/logo.png');
@endphp

<div class="top-bar-area">
    <div class="container">
        <div class="row align-center">
            <div class="col-lg-3 logo">
                <a href="{{ route('home') }}">
                    <img src="{{ $logoUrl }}" class="logo" alt="{{ $websiteSettings->siteName() }}">
                </a>
            </div>
            <div class="col-lg-9 address-info text-right">
                <div class="info box">
                    <ul>
                        @if ($websiteSettings->contact_email)
                            <li>
                                <div class="icon"><i class="flaticon-email"></i></div>
                                <div class="info"><span>Email</span> {{ $websiteSettings->contact_email }}</div>
                            </li>
                        @endif
                        @if ($websiteSettings->contact_phone)
                            <li>
                                <div class="icon"><i class="flaticon-call"></i></div>
                                <div class="info"><span>Telepon</span> {{ $websiteSettings->contact_phone }}</div>
                            </li>
                        @endif
                        @if ($websiteSettings->contact_address)
                            <li>
                                <div class="icon"><i class="flaticon-clock-1"></i></div>
                                <div class="info"><span>Alamat</span> {{ \Illuminate\Support\Str::limit($websiteSettings->contact_address, 40) }}</div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<header>
    <div class="container box-nav">
        <div class="row">
            <nav id="mainNav" class="navbar logo-less top-less navbar-default navbar-fixed white bootsnav on no-full nav-box no-background">
                <div class="container">
                    <div class="attr-nav contact">
                        <ul>
                            <li>
                                <i class="fas fa-stethoscope"></i>
                                <p>
                                    {{ $websiteSettings->shortName() }}
                                    <span>{{ $websiteSettings->contact_phone ?: 'Layanan RSUD' }}</span>
                                </p>
                            </li>
                        </ul>
                    </div>

                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
                            <i class="fa fa-bars"></i>
                        </button>
                        <a class="navbar-brand" href="{{ route('home') }}">
                            <img src="{{ $logoUrl }}" class="logo" alt="{{ $websiteSettings->siteName() }}">
                        </a>
                    </div>

                    <div class="collapse navbar-collapse" id="navbar-menu">
                        <ul class="nav navbar-nav navbar-left" data-in="fadeInDown" data-out="fadeOutUp">
                            <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                                <a href="{{ route('home') }}">Beranda</a>
                            </li>

                            @if (($profilPages ?? collect())->isNotEmpty())
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Profil</a>
                                    <ul class="dropdown-menu">
                                        @foreach ($profilPages as $profilPage)
                                            <li><a href="{{ route('public.profil.show', $profilPage->slug) }}">{{ $profilPage->title }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif

                            <li><a href="{{ route('public.berita.index') }}">Berita</a></li>
                            <li><a href="{{ route('public.agenda.index') }}">Agenda</a></li>
                            <li><a href="{{ route('public.galeri.index') }}">Galeri</a></li>
                            <li><a href="{{ route('public.pengumuman.index') }}">Pengumuman</a></li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Layanan</a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('public.layanan.index') }}">Daftar Layanan</a></li>
                                    <li><a href="{{ route('public.layanan.check-status') }}">Cek Status</a></li>
                                </ul>
                            </li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dokter</a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('public.dokter.index') }}">Daftar Dokter</a></li>
                                    <li><a href="{{ route('public.dokter.jadwal') }}">Jadwal Dokter</a></li>
                                </ul>
                            </li>

                            <li><a href="{{ route('public.pegawai.index') }}">Pegawai</a></li>

                            @foreach ($navMenus ?? [] as $navMenu)
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $navMenu->title }}</a>
                                    <ul class="dropdown-menu">
                                        @foreach ($navMenu->pages as $navPage)
                                            <li><a href="{{ route('public.menu-page.show', [$navMenu->slug, $navPage->slug]) }}">{{ $navPage->title }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach

                            <li><a href="{{ route('public.kontak.index') }}">Kontak</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="clearfix"></div>
        </div>
    </div>
</header>
