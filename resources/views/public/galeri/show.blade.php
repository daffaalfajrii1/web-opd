@extends('public.layouts.clinicom')

@section('title', $galeri->title)
@section('meta_description', $galeri->description ?: 'Galeri '.$galeri->title.' — '.$websiteSettings->siteName())
@section('meta_type', 'article')
@section('meta_url', route('public.galeri.show', $galeri->slug))
@section('meta_image', $galeri->thumbnailUrl() ?: $websiteSettings->defaultShareImageUrl())

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => $galeri->title,
        'breadcrumbs' => [
            ['label' => 'Galeri', 'url' => route('public.galeri.index')],
            ['label' => \Illuminate\Support\Str::limit($galeri->title, 48)],
        ],
    ])

    <section class="rsud-strip rsud-strip--soft rsud-galeri-detail">
        <div class="container">
            <div class="rsud-article__card mb-4">
                <div class="rsud-galeri-detail__banner">
                    <div class="rsud-galeri-detail__banner-media">
                        <img src="{{ $galeri->thumbnailUrl() ?? asset('clinicom/assets/img/800x600.png') }}" alt="{{ $galeri->title }}">
                    </div>
                    <div class="rsud-galeri-detail__banner-body">
                        @if ($galeri->is_featured)
                            <span class="rsud-badge rsud-badge--accent">Unggulan</span>
                        @endif
                        <h1 class="rsud-galeri-detail__title">{{ $galeri->title }}</h1>
                        @if ($galeri->description)
                            <p>{{ $galeri->description }}</p>
                        @endif
                        <div class="rsud-galeri-detail__meta">
                            @if ($galeri->event_date)
                                <span><i class="far fa-calendar-alt"></i> {{ $galeri->event_date->translatedFormat('d F Y') }}</span>
                            @endif
                            <span><i class="far fa-images"></i> {{ $galeri->itemsCountLabel() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($galeri->photos->isNotEmpty())
                <div class="rsud-galeri-detail__section">
                    <div class="rsud-strip__head">
                        <div>
                            <p class="rsud-kicker">Album</p>
                            <h2 class="rsud-title">Foto</h2>
                        </div>
                        <small class="text-muted">Klik foto untuk memperbesar</small>
                    </div>
                    @include('public.partials.clinicom.galeri-photo-grid', [
                        'photos' => $galeri->photos,
                        'galeriTitle' => $galeri->title,
                    ])
                </div>
            @endif

            @if ($galeri->videos->isNotEmpty())
                <div class="rsud-galeri-detail__section mt-4">
                    <div class="rsud-strip__head">
                        <div>
                            <p class="rsud-kicker">Multimedia</p>
                            <h2 class="rsud-title">Video</h2>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($galeri->videos as $video)
                            <div class="col-lg-6 mb-4">
                                <a
                                    href="{{ $video->youtube_url ?: 'https://www.youtube.com/watch?v='.$video->youtube_video_id }}"
                                    class="rsud-galeri-video popup-youtube"
                                >
                                    <span class="rsud-galeri-video__media">
                                        <img
                                            src="{{ $video->thumbnailUrl() ?? asset('clinicom/assets/img/800x600.png') }}"
                                            alt="{{ $video->title ?: $galeri->title }}"
                                            loading="lazy"
                                        >
                                        <span class="rsud-galeri-video__play" aria-hidden="true">
                                            <i class="fas fa-play"></i>
                                        </span>
                                    </span>
                                    @if ($video->title)
                                        <span class="rsud-galeri-video__title">{{ $video->title }}</span>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($galeri->photos->isEmpty() && $galeri->videos->isEmpty())
                <div class="rsud-empty">
                    <p class="mb-0">Belum ada media dalam galeri ini.</p>
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('public.galeri.index') }}" class="btn rsud-btn-outline">Semua Galeri</a>
            </div>
        </div>
    </section>
@endsection
