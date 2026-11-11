@extends('public.layouts.optilux')

@section('title', $galeri->title)
@section('meta_description', $galeri->description ?: 'Galeri '.$galeri->title.' — '.$websiteSettings->siteName())
@section('meta_type', 'article')
@section('meta_url', route('public.galeri.show', $galeri->slug))
@section('meta_image', $galeri->thumbnailUrl() ?: $websiteSettings->defaultShareImageUrl())

@section('content')
    @include('public.partials.optilux.subheader', [
        'title' => $galeri->title,
        'breadcrumbs' => [
            ['label' => 'Galeri', 'url' => route('public.galeri.index')],
            ['label' => $galeri->title],
        ],
    ])

    <section>
        <div class="container">
            <div class="kecamatan-galeri-detail">
                @if ($galeri->description)
                    <p class="lead mb-3">{{ $galeri->description }}</p>
                @endif

                <div class="kecamatan-galeri-detail__meta mb-4">
                    @if ($galeri->event_date)
                        <span><i class="icofont-calendar id-color me-1"></i>{{ $galeri->event_date->translatedFormat('d F Y') }}</span>
                    @endif
                    <span><i class="icofont-image id-color me-1"></i>{{ $galeri->itemsCountLabel() }}</span>
                </div>

                @if ($galeri->photos->isNotEmpty())
                    <div class="kecamatan-galeri-detail__section">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <h4 class="mb-0">Foto</h4>
                            <small class="text-muted">Klik foto untuk memperbesar</small>
                        </div>
                        @include('public.partials.optilux.galeri-photo-grid', [
                            'photos' => $galeri->photos,
                            'galeriTitle' => $galeri->title,
                        ])
                    </div>
                @endif

                @if ($galeri->videos->isNotEmpty())
                    <div class="kecamatan-galeri-detail__section">
                        <h4 class="mb-3">Video</h4>
                        <div class="row g-4">
                            @foreach ($galeri->videos as $video)
                                <div class="col-lg-6">
                                    <a
                                        href="{{ $video->youtube_url ?: 'https://www.youtube.com/watch?v='.$video->youtube_video_id }}"
                                        class="kecamatan-galeri-video-card popup-youtube"
                                    >
                                        <span class="kecamatan-galeri-video-card__thumb">
                                            <img
                                                src="{{ $video->thumbnailUrl() ?? asset('optilux/images/gallery/1.webp') }}"
                                                alt="{{ $video->title ?: $galeri->title }}"
                                                loading="lazy"
                                            >
                                            <span class="kecamatan-galeri-video-card__play" aria-hidden="true">
                                                <i class="icofont-ui-play"></i>
                                            </span>
                                        </span>
                                        @if ($video->title)
                                            <span class="kecamatan-galeri-video-card__title">{{ $video->title }}</span>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($galeri->photos->isEmpty() && $galeri->videos->isEmpty())
                    <div class="bg-light p-5 rounded-1 text-center">
                        <p class="mb-0">Belum ada media dalam galeri ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
