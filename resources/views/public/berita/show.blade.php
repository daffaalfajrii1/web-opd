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
        'thumbnailUrl' => $berita->thumbnailUrl(),
        'breadcrumbs' => [
            ['label' => 'Berita', 'url' => route('public.berita.index')],
            ['label' => $berita->title],
        ],
    ])

    <section>
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-8">
                    <div class="blog-read kecamatan-content">
                        @include('public.partials.optilux.berita-author', ['berita' => $berita])

                        <div class="mb-4 mt-3 text-muted kecamatan-berita-meta">
                            @if ($berita->published_at)
                                <span><i class="icofont-calendar id-color me-1"></i>{{ $berita->published_at->translatedFormat('d F Y') }}</span>
                            @endif
                            @if ($berita->category)
                                <span class="ms-3"><i class="icofont-tag id-color me-1"></i>{{ $berita->category->name }}</span>
                            @endif
                            <span class="ms-3"><i class="icofont-eye id-color me-1"></i>{{ $berita->views_count }} kali dibaca</span>
                        </div>

                        {!! $berita->content !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    @if ($related->isNotEmpty())
                        <h4 class="mb-3">Berita Terkait</h4>
                        @foreach ($related as $item)
                            <div class="mb-3">
                                <a href="{{ route('public.berita.show', $item->slug) }}" class="text-dark fw-bold">{{ $item->title }}</a>
                                @if ($item->published_at)
                                    <div class="small text-muted">{{ $item->published_at->translatedFormat('d M Y') }}</div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
