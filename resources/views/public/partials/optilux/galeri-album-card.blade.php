@php
    $headingTag = $headingTag ?? 'h4';
@endphp

<a href="{{ route('public.galeri.show', $galeri->slug) }}" class="kecamatan-galeri-album-card hover d-block h-100">
    <div class="kecamatan-galeri-album-card__thumb">
        <img src="{{ $galeri->thumbnailUrl() ?? asset('optilux/images/gallery/1.webp') }}" alt="{{ $galeri->title }}" class="hover-scale-1-1">
        <span class="kecamatan-galeri-album-card__overlay">
            <span class="kecamatan-galeri-album-card__cta">Lihat Album</span>
        </span>
        @if ($galeri->is_featured)
            <span class="kecamatan-galeri-album-card__badge">Unggulan</span>
        @endif
    </div>
    <div class="kecamatan-galeri-album-card__body">
        <{{ $headingTag }} class="kecamatan-galeri-album-card__title">{{ $galeri->title }}</{{ $headingTag }}>
        @if ($galeri->event_date)
            <small class="kecamatan-galeri-album-card__date">
                <i class="icofont-calendar id-color me-1"></i>{{ $galeri->event_date->translatedFormat('d F Y') }}
            </small>
        @endif
        <small class="kecamatan-galeri-album-card__meta">{{ $galeri->itemsCountLabel() }}</small>
        @if ($galeri->description)
            <p class="kecamatan-galeri-album-card__desc">{{ \Illuminate\Support\Str::limit($galeri->description, 100) }}</p>
        @endif
    </div>
</a>
