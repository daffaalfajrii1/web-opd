@extends('public.layouts.optilux')

@section('title', $pengumuman->title)
@section('meta_description', $pengumuman->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $pengumuman->content), 160))
@section('meta_type', 'article')
@section('meta_url', route('public.pengumuman.show', $pengumuman->slug))
@section('meta_image', $websiteSettings->defaultShareImageUrl())

@section('content')
    @include('public.partials.optilux.subheader', [
        'title' => $pengumuman->title,
        'breadcrumbs' => [
            ['label' => 'Pengumuman', 'url' => route('public.pengumuman.index')],
            ['label' => $pengumuman->title],
        ],
    ])

    <section>
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-8">
                    <div class="mb-3">
                        @if ($pengumuman->isPenting())
                            <span class="kecamatan-badge-penting me-1">Penting</span>
                        @endif
                        @if ($pengumuman->published_at)
                            <span class="text-muted"><i class="icofont-calendar id-color me-1"></i>{{ $pengumuman->published_at->translatedFormat('d F Y') }}</span>
                        @endif
                        @if ($pengumuman->valid_until)
                            <span class="text-muted ms-3">Berlaku hingga {{ $pengumuman->valid_until->translatedFormat('d F Y') }}</span>
                        @endif
                    </div>

                    <div class="kecamatan-content">
                        {!! $pengumuman->content !!}
                    </div>

                    @if ($pengumuman->attachments->isNotEmpty())
                        <div class="mt-5">
                            <h4 class="mb-3">Lampiran</h4>
                            <div class="kecamatan-download-list">
                                @foreach ($pengumuman->attachments as $attachment)
                                    <div class="kecamatan-download-item">
                                        <div class="kecamatan-download-item__info">
                                            <div class="kecamatan-download-item__icon">
                                                <i class="icofont-file-pdf"></i>
                                            </div>
                                            <div>
                                                <div class="kecamatan-download-item__title">{{ $attachment->displayTitle() }}</div>
                                                <div class="kecamatan-download-item__size">{{ $attachment->formattedFileSize() }}</div>
                                            </div>
                                        </div>
                                        <a href="{{ $attachment->downloadUrl() }}" class="kecamatan-download-btn" target="_blank" rel="noopener" download>
                                            <i class="icofont-download"></i> Unduh
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
