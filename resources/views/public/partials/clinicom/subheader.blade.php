<div class="breadcrumb-area rsud-page-hero shadow dark text-light text-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h1>{{ $title }}</h1>
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Beranda</a></li>
                    @foreach ($breadcrumbs ?? [] as $crumb)
                        @if ($loop->last)
                            <li class="active">{{ $crumb['label'] }}</li>
                        @else
                            <li>
                                @if (!empty($crumb['url']))
                                    <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                @else
                                    {{ $crumb['label'] }}
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
