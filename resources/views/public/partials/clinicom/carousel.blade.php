@if (($websiteCarousels ?? collect())->isNotEmpty())
    <section class="rsud-carousel" aria-label="Banner">
        <div id="rsudCarousel" class="carousel slide" data-ride="carousel" data-interval="{{ $websiteSettings->carouselDurationMs() }}">
            <div class="carousel-inner">
                @foreach ($websiteCarousels as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="rsud-carousel__slide">
                            <img class="rsud-carousel__image" src="{{ $slide->imageUrl() }}" alt="{{ $slide->title ?: $websiteSettings->siteName() }}">
                            @if ($slide->title || $slide->caption || $slide->link_url)
                                <div class="rsud-carousel__overlay">
                                    <div class="container">
                                        <div class="rsud-carousel__content">
                                            @if ($slide->title)
                                                <h2>{{ $slide->title }}</h2>
                                            @endif
                                            @if ($slide->caption)
                                                <p>{{ $slide->caption }}</p>
                                            @endif
                                            @if ($slide->link_url)
                                                <a class="btn btn-md btn-gradient" href="{{ $slide->link_url }}">Selengkapnya</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($websiteCarousels->count() > 1)
                <a class="carousel-control-prev rsud-carousel__control" href="#rsudCarousel" role="button" data-slide="prev">
                    <span class="fas fa-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next rsud-carousel__control" href="#rsudCarousel" role="button" data-slide="next">
                    <span class="fas fa-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            @endif
        </div>
    </section>
@else
    <section class="rsud-carousel rsud-carousel--fallback" aria-label="Banner">
        <div class="container">
            <div class="rsud-carousel__fallback">
                <h2>{{ $websiteSettings->siteName() }}</h2>
                <p>{{ $websiteSettings->site_tagline ?: 'Website resmi layanan kesehatan Kabupaten Rejang Lebong.' }}</p>
            </div>
        </div>
    </section>
@endif
