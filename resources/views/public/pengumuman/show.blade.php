@extends('public.layouts.clinicom')

@section('title', $pengumuman->title)
@section('meta_description', $pengumuman->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $pengumuman->content), 160))
@section('meta_type', 'article')
@section('meta_url', route('public.pengumuman.show', $pengumuman->slug))
@section('meta_image', $websiteSettings->defaultShareImageUrl())

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Detail Pengumuman',
        'breadcrumbs' => [
            ['label' => 'Pengumuman', 'url' => route('public.pengumuman.index')],
            ['label' => \Illuminate\Support\Str::limit($pengumuman->title, 48)],
        ],
    ])

    <section class="rsud-article rsud-announce-detail">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <article class="rsud-article__card">
                        <div class="rsud-announce-detail__banner">
                            <div class="rsud-announce-detail__banner-icon" aria-hidden="true">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div>
                                <div class="rsud-announce-detail__badges">
                                    @if ($pengumuman->isPenting())
                                        <span class="rsud-badge rsud-badge--danger">Penting</span>
                                    @endif
                                    @if ($pengumuman->is_featured)
                                        <span class="rsud-badge rsud-badge--accent">Unggulan</span>
                                    @endif
                                    <span class="rsud-badge rsud-badge--soft">Pengumuman</span>
                                </div>
                                <h1 class="rsud-announce-detail__title">{{ $pengumuman->title }}</h1>
                            </div>
                        </div>

                        <div class="rsud-article__body">
                            <div class="rsud-article__meta">
                                @if ($pengumuman->published_at)
                                    <span><i class="fas fa-calendar-alt"></i> {{ $pengumuman->published_at->translatedFormat('d F Y') }}</span>
                                @endif
                                @if ($pengumuman->valid_until)
                                    <span><i class="fas fa-hourglass-half"></i> Berlaku hingga {{ $pengumuman->valid_until->translatedFormat('d F Y') }}</span>
                                @endif
                                <span><i class="fas fa-paperclip"></i> {{ $pengumuman->attachmentsCountLabel() }}</span>
                            </div>

                            @if ($pengumuman->excerpt)
                                <p class="rsud-article__excerpt">{{ $pengumuman->excerpt }}</p>
                            @endif

                            <div class="rsud-article__content kecamatan-content">
                                {!! $pengumuman->content !!}
                            </div>

                            @if ($pengumuman->attachments->isNotEmpty())
                                <div class="rsud-announce-files">
                                    <h3 class="rsud-announce-files__title">
                                        <i class="fas fa-folder-open"></i> Lampiran
                                    </h3>
                                    <div class="rsud-announce-files__list">
                                        @foreach ($pengumuman->attachments as $attachment)
                                            <div class="rsud-announce-file">
                                                <div class="rsud-announce-file__info">
                                                    <div class="rsud-announce-file__icon" aria-hidden="true">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $attachment->displayTitle() }}</strong>
                                                        <small>{{ $attachment->formattedFileSize() }}</small>
                                                    </div>
                                                </div>
                                                <a href="{{ $attachment->downloadUrl() }}" class="btn btn-gradient btn-sm" target="_blank" rel="noopener" download>
                                                    <i class="fas fa-download"></i> Unduh
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>
                </div>

                <div class="col-lg-4">
                    <aside class="rsud-article__sidebar">
                        <h4>Info ringkas</h4>
                        <ul class="rsud-announce-aside">
                            <li>
                                <span>Status</span>
                                <strong>{{ $pengumuman->isPenting() ? 'Penting' : 'Normal' }}{{ $pengumuman->is_featured ? ' · Unggulan' : '' }}</strong>
                            </li>
                            <li>
                                <span>Tanggal terbit</span>
                                <strong>{{ optional($pengumuman->published_at)->translatedFormat('d F Y') ?: '—' }}</strong>
                            </li>
                            <li>
                                <span>Berlaku hingga</span>
                                <strong>{{ optional($pengumuman->valid_until)->translatedFormat('d F Y') ?: 'Tidak terbatas' }}</strong>
                            </li>
                            <li>
                                <span>Lampiran</span>
                                <strong>{{ $pengumuman->attachmentsCountLabel() }}</strong>
                            </li>
                        </ul>

                        @if (($relatedPengumumans ?? collect())->isNotEmpty())
                            <hr class="rsud-aside-divider">
                            <h4>Pengumuman lain</h4>
                            @foreach ($relatedPengumumans as $item)
                                <a href="{{ route('public.pengumuman.show', $item->slug) }}" class="rsud-related-item">
                                    <div class="rsud-related-item__thumb rsud-related-item__thumb--icon">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <div>
                                        <strong>{{ \Illuminate\Support\Str::limit($item->title, 58) }}</strong>
                                        @if ($item->published_at)
                                            <small>{{ $item->published_at->translatedFormat('d M Y') }}</small>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        @endif

                        <a href="{{ route('public.pengumuman.index') }}" class="btn rsud-btn-outline btn-block mt-3">
                            Semua Pengumuman
                        </a>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
