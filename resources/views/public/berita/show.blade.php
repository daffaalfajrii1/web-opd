@extends('public.layouts.clinicom')

@section('title', $berita->title)
@section('meta_description', $berita->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($berita->content), 160))
@section('meta_type', 'article')
@section('meta_url', route('public.berita.show', $berita->slug))
@section('meta_image', $berita->thumbnailUrl() ?: $websiteSettings->defaultShareImageUrl())

@push('seo')
    @if ($berita->published_at)
        <meta property="article:published_time" content="{{ $berita->published_at->toAtomString() }}">
    @endif
    @if ($berita->updated_at)
        <meta property="article:modified_time" content="{{ $berita->updated_at->toAtomString() }}">
    @endif
    @if ($berita->category)
        <meta property="article:section" content="{{ $berita->category->name }}">
    @endif
    <meta property="article:author" content="{{ $berita->authorName() }}">
@endpush

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => $berita->title,
        'breadcrumbs' => [
            ['label' => 'Berita', 'url' => route('public.berita.index')],
            ['label' => \Illuminate\Support\Str::limit($berita->title, 48)],
        ],
    ])

    <section class="rsud-article">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <article class="rsud-article__card">
                        @if ($berita->thumbnailUrl())
                            <div class="rsud-article__hero">
                                <img src="{{ $berita->thumbnailUrl() }}" alt="{{ $berita->title }}">
                            </div>
                        @endif

                        <div class="rsud-article__body">
                            <div class="rsud-article__meta">
                                @if ($berita->published_at)
                                    <span><i class="fas fa-calendar-alt"></i> {{ $berita->published_at->translatedFormat('d F Y') }}</span>
                                @endif
                                @if ($berita->category)
                                    <span><i class="fas fa-tag"></i> {{ $berita->category->name }}</span>
                                @endif
                                <span><i class="fas fa-eye"></i> {{ number_format($berita->views_count) }} kali dibaca</span>
                            </div>

                            <div class="rsud-article__author">
                                <div class="rsud-article__avatar">
                                    @if ($berita->authorAvatarUrl())
                                        <img src="{{ $berita->authorAvatarUrl() }}" alt="{{ $berita->authorName() }}">
                                    @else
                                        <span>{{ $berita->authorInitial() }}</span>
                                    @endif
                                </div>
                                <div>
                                    <small>Ditulis oleh</small>
                                    <strong>{{ $berita->authorName() }}</strong>
                                    @if ($berita->authorRoleLabel())
                                        <div class="text-muted small">{{ $berita->authorRoleLabel() }}</div>
                                    @endif
                                </div>
                            </div>

                            @if ($berita->excerpt)
                                <p class="rsud-article__excerpt">{{ $berita->excerpt }}</p>
                            @endif

                            <div class="rsud-article__content kecamatan-content">
                                {!! $berita->content !!}
                            </div>
                        </div>
                    </article>
                </div>

                <div class="col-lg-4">
                    <aside class="rsud-article__sidebar">
                        <h4>Berita Terkait</h4>
                        @forelse ($related as $item)
                            <a href="{{ route('public.berita.show', $item->slug) }}" class="rsud-related-item">
                                <div class="rsud-related-item__thumb">
                                    @if ($item->thumbnailUrl())
                                        <img src="{{ $item->thumbnailUrl() }}" alt="{{ $item->title }}">
                                    @else
                                        <span class="rsud-berita-card__fallback"><i class="fas fa-newspaper"></i></span>
                                    @endif
                                </div>
                                <div>
                                    <strong>{{ $item->title }}</strong>
                                    @if ($item->published_at)
                                        <small>{{ $item->published_at->translatedFormat('d M Y') }}</small>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p class="text-muted mb-0">Belum ada berita terkait.</p>
                        @endforelse
                        <a href="{{ route('public.berita.index') }}" class="btn btn-gradient btn-sm mt-3">Semua Berita</a>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
