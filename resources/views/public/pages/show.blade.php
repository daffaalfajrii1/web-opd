@extends('public.layouts.clinicom')

@section('title', $page->title)
@section('meta_description', $page->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160))
@section('meta_type', 'article')
@section('meta_url', route('public.profil.show', $page->slug))
@section('meta_image', $websiteSettings->defaultShareImageUrl())

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => $page->title,
        'breadcrumbs' => [
            ['label' => 'Profil', 'url' => $profilPages->first() ? route('public.profil.show', $profilPages->first()->slug) : route('home')],
            ['label' => $page->title],
        ],
    ])

    <section>
        <div class="container">
            <div class="row gx-5">
                @if ($profilPages->count() > 1)
                    <div class="col-lg-3">
                        <div class="me-lg-3">
                            @foreach ($profilPages as $profilPage)
                                <a
                                    href="{{ route('public.profil.show', $profilPage->slug) }}"
                                    @class([
                                        'd-block p-3 px-4 rounded-10px mb-3',
                                        'bg-color text-light' => $profilPage->id === $page->id,
                                        'bg-light' => $profilPage->id !== $page->id,
                                    ])
                                >
                                    <h5 class="mb-0">{{ $profilPage->title }}</h5>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-9">
                        @if ($page->excerpt)
                            <p class="lead">{{ $page->excerpt }}</p>
                        @endif
                        <div class="kecamatan-content">
                            {!! $page->content !!}
                        </div>
                    </div>
                @else
                    <div class="col-lg-12">
                        @if ($page->excerpt)
                            <p class="lead">{{ $page->excerpt }}</p>
                        @endif
                        <div class="kecamatan-content">
                            {!! $page->content !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
