<div class="row g-3 g-md-4 kecamatan-galeri-photo-grid zoom-gallery">
    @foreach ($photos as $photo)
        <div class="col-lg-3 col-md-4 col-6">
            <a
                href="{{ $photo->imageUrl() }}"
                title="{{ $photo->title ?: $galeriTitle }}"
                class="kecamatan-galeri-photo"
            >
                <img src="{{ $photo->imageUrl() }}" alt="{{ $photo->title ?: $galeriTitle }}" loading="lazy">
                <span class="kecamatan-galeri-photo__overlay" aria-hidden="true">
                    <span class="kecamatan-galeri-photo__icon"><i class="icofont-search-2"></i></span>
                </span>
            </a>
            @if ($photo->title)
                <p class="kecamatan-galeri-photo__caption">{{ $photo->title }}</p>
            @endif
        </div>
    @endforeach
</div>
