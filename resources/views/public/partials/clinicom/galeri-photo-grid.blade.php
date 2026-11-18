<div class="row rsud-galeri-grid">
    @foreach ($photos as $photo)
        <div class="col-lg-3 col-md-4 col-6 mb-4">
            <a
                href="{{ $photo->imageUrl() }}"
                title="{{ $photo->title ?: $galeriTitle }}"
                class="rsud-galeri-photo popup-gallery"
            >
                <img src="{{ $photo->imageUrl() }}" alt="{{ $photo->title ?: $galeriTitle }}" loading="lazy">
                <span class="rsud-galeri-photo__overlay" aria-hidden="true">
                    <i class="fas fa-search-plus"></i>
                </span>
            </a>
            @if ($photo->title)
                <p class="rsud-galeri-photo__caption">{{ $photo->title }}</p>
            @endif
        </div>
    @endforeach
</div>
