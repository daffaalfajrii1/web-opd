<footer class="bg-dark text-light">
    <div class="container">
        <div class="f-items default-padding">
            <div class="row">
                <div class="single-item col-lg-4 col-md-6 item">
                    <div class="f-item about">
                        <h4 class="widget-title">{{ $websiteSettings->siteName() }}</h4>
                        <p>{{ $websiteSettings->site_tagline ?: ($websiteSettings->footer_text ?: 'Portal informasi dan layanan rumah sakit.') }}</p>
                        @if ($websiteSettings->whatsappUrl())
                            <a class="btn btn-md left-icon btn-gradient" href="{{ $websiteSettings->whatsappAduanUrl() }}" target="_blank" rel="noopener">
                                <i class="fab fa-whatsapp"></i> Aduan WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
                <div class="single-item col-lg-4 col-md-6 item">
                    <div class="f-item link">
                        <h4 class="widget-title">Menu</h4>
                        <ul>
                            <li><a href="{{ route('public.dokter.index') }}">Dokter</a></li>
                            <li><a href="{{ route('public.dokter.jadwal') }}">Jadwal Dokter</a></li>
                            <li><a href="{{ route('public.layanan.index') }}">Layanan</a></li>
                            <li><a href="{{ route('public.berita.index') }}">Berita</a></li>
                            <li><a href="{{ route('public.kontak.index') }}">Kontak</a></li>
                        </ul>
                    </div>
                </div>
                <div class="single-item col-lg-4 col-md-6 item">
                    <div class="f-item contact">
                        <h4 class="widget-title">Kontak</h4>
                        <ul>
                            @if ($websiteSettings->contact_address)
                                <li><i class="fas fa-map-marker-alt"></i> {{ $websiteSettings->contact_address }}</li>
                            @endif
                            @if ($websiteSettings->contact_phone)
                                <li><i class="fas fa-phone"></i> {{ $websiteSettings->contact_phone }}</li>
                            @endif
                            @if ($websiteSettings->contact_email)
                                <li><i class="fas fa-envelope"></i> {{ $websiteSettings->contact_email }}</li>
                            @endif
                        </ul>
                        <ul class="list-inline rsud-footer-social mt-3 mb-0">
                            @if ($websiteSettings->social_facebook)
                                <li class="list-inline-item">
                                    <a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                </li>
                            @endif
                            @if ($websiteSettings->social_instagram)
                                <li class="list-inline-item">
                                    <a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                </li>
                            @endif
                            @if ($websiteSettings->social_youtube)
                                <li class="list-inline-item">
                                    <a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>&copy; {{ date('Y') }} {{ $websiteSettings->siteName() }}. {{ $websiteSettings->footer_text }}</p>
                </div>
            </div>
        </div>
    </div>
</footer>
