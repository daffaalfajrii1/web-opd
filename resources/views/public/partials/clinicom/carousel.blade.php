@if ($websiteCarousels->isNotEmpty())
    <div class="banner-area">
        <div id="bootcarousel" class="carousel responsive-top-pad-110p text-large slide carousel-fade animate_text" data-ride="carousel" data-interval="{{ $websiteSettings->carouselDurationMs() }}">
            <div class="carousel-inner carousel-zoom">
                @foreach ($websiteCarousels as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="slider-thumb bg-cover" style="background-image: url({{ $slide->imageUrl() }});"></div>
                        <div class="box-table">
                            <div class="box-cell">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-9">
                                            <div class="content">
                                                @if ($slide->title)
                                                    <h2 data-animation="animated slideInDown">{{ $slide->title }}</h2>
                                                @endif
                                                @if ($slide->caption)
                                                    <p data-animation="animated slideInLeft">{{ $slide->caption }}</p>
                                                @endif
                                                @if ($slide->link_url)
                                                    <a data-animation="animated fadeInUp" class="btn btn-md btn-gradient" href="{{ $slide->link_url }}">Selengkapnya</a>
                                                @else
                                                    <a data-animation="animated fadeInUp" class="btn btn-md btn-gradient" href="{{ route('public.dokter.index') }}">Cari Dokter</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($websiteCarousels->count() > 1)
                <a class="left carousel-control light" href="#bootcarousel" data-slide="prev">
                    <i class="fa fa-angle-left"></i>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control light" href="#bootcarousel" data-slide="next">
                    <i class="fa fa-angle-right"></i>
                    <span class="sr-only">Next</span>
                </a>
            @endif
        </div>
    </div>
@endif
