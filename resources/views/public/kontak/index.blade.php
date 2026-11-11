@extends('public.layouts.optilux')

@section('title', 'Kontak')

@section('content')
    @include('public.partials.optilux.subheader', [
        'title' => 'Kontak',
        'breadcrumbs' => [['label' => 'Kontak']],
    ])

    <section>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <h4 class="mb-3">{{ $websiteSettings->siteName() }}</h4>
                    @if ($websiteSettings->contact_address)
                        <p class="mb-3">
                            <i class="icofont-location-pin id-color me-2"></i>
                            {{ $websiteSettings->contact_address }}
                        </p>
                    @endif
                    @if ($websiteSettings->contact_phone)
                        <p class="mb-3">
                            <i class="icofont-phone id-color me-2"></i>
                            {{ $websiteSettings->contact_phone }}
                        </p>
                    @endif
                    @if ($websiteSettings->contact_email)
                        <p class="mb-3">
                            <i class="icofont-envelope id-color me-2"></i>
                            <a href="mailto:{{ $websiteSettings->contact_email }}">{{ $websiteSettings->contact_email }}</a>
                        </p>
                    @endif
                    @if ($websiteSettings->whatsappUrl())
                        <p class="mb-3">
                            <i class="fa-brands fa-whatsapp id-color me-2"></i>
                            <a href="{{ $websiteSettings->whatsappUrl() }}" target="_blank" rel="noopener">WhatsApp</a>
                        </p>
                    @endif

                    <div class="social-icons mt-4">
                        @if ($websiteSettings->social_facebook)
                            <a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>
                        @endif
                        @if ($websiteSettings->social_instagram)
                            <a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a>
                        @endif
                        @if ($websiteSettings->social_youtube)
                            <a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener"><i class="fa-brands fa-youtube"></i></a>
                        @endif
                    </div>
                </div>

                @if ($websiteSettings->mapsIframeSrc())
                    <div class="col-lg-7 kecamatan-map-embed">
                        <iframe
                            src="{{ $websiteSettings->mapsIframeSrc() }}"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Peta {{ $websiteSettings->siteName() }}"
                        ></iframe>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
