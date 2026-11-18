<a href="{{ route('public.galeri.show', $galeri->slug) }}" class="rsud-galeri-album">
    <span class="rsud-galeri-album__media">
        <img src="{{ $galeri->thumbnailUrl() ?? asset('clinicom/assets/img/800x600.png') }}" alt="{{ $galeri->title }}" loading="lazy">
        <span class="rsud-galeri-album__overlay">
            <span>Lihat Album</span>
        </span>
        @if ($galeri->is_featured)
            <span class="rsud-badge rsud-badge--accent rsud-galeri-album__badge">Unggulan</span>
        @endif
    </span>
    <span class="rsud-galeri-album__body">
        <strong class="rsud-galeri-album__title">{{ $galeri->title }}</strong>
        <span class="rsud-galeri-album__meta">
            @if ($galeri->event_date)
                <span><i class="far fa-calendar-alt"></i> {{ $galeri->event_date->translatedFormat('d M Y') }}</span>
            @endif
            <span><i class="far fa-images"></i> {{ $galeri->itemsCountLabel() }}</span>
        </span>
        @if ($galeri->description)
            <span class="rsud-galeri-album__desc">{{ \Illuminate\Support\Str::limit($galeri->description, 90) }}</span>
        @endif
    </span>
</a>
