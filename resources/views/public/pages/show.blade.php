@extends('public.layouts.clinicom')

@php
    $isNavPage = isset($navMenu) && $navMenu;
    $pageUrl = $isNavPage
        ? route('public.menu-page.show', [$navMenu->slug, $page->slug])
        : route('public.profil.show', $page->slug);
    $siblingPages = $isNavPage
        ? ($navMenu->relationLoaded('pages')
            ? $navMenu->pages
            : $navMenu->pages()->published()->ordered()->get(['id', 'title', 'slug', 'nav_menu_id']))
        : $profilPages;
@endphp

@section('title', $page->title)
@section('meta_description', $page->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160))
@section('meta_type', 'article')
@section('meta_url', $pageUrl)
@section('meta_image', $websiteSettings->defaultShareImageUrl())

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => $page->title,
        'breadcrumbs' => $isNavPage
            ? [
                ['label' => $navMenu->title, 'url' => $siblingPages->first()
                    ? route('public.menu-page.show', [$navMenu->slug, $siblingPages->first()->slug])
                    : route('home')],
                ['label' => $page->title],
            ]
            : [
                ['label' => 'Profil', 'url' => $profilPages->first() ? route('public.profil.show', $profilPages->first()->slug) : route('home')],
                ['label' => $page->title],
            ],
    ])

    <section class="rsud-strip rsud-strip--soft">
        <div class="container">
            <div class="row">
                @if ($siblingPages->count() > 1)
                    <div class="col-lg-3 mb-4 mb-lg-0">
                        <aside class="rsud-article__sidebar">
                            <h4>{{ $isNavPage ? $navMenu->title : 'Profil' }}</h4>
                            <div class="rsud-service-nav">
                                @foreach ($siblingPages as $sibling)
                                    <a
                                        href="{{ $isNavPage
                                            ? route('public.menu-page.show', [$navMenu->slug, $sibling->slug])
                                            : route('public.profil.show', $sibling->slug) }}"
                                        @class(['rsud-service-nav__item', 'is-active' => $sibling->id === $page->id])
                                    >
                                        {{ $sibling->title }}
                                    </a>
                                @endforeach
                            </div>
                        </aside>
                    </div>
                    <div class="col-lg-9">
                        <article class="rsud-article__card">
                            <div class="rsud-article__body">
                                @if ($page->excerpt)
                                    <p class="rsud-article__excerpt">{{ $page->excerpt }}</p>
                                @endif
                                <div class="rsud-article__content kecamatan-content">
                                    {!! $page->content !!}
                                </div>
                            </div>
                        </article>
                    </div>
                @else
                    <div class="col-lg-10 mx-auto">
                        <article class="rsud-article__card">
                            <div class="rsud-article__body">
                                @if ($page->excerpt)
                                    <p class="rsud-article__excerpt">{{ $page->excerpt }}</p>
                                @endif
                                <div class="rsud-article__content kecamatan-content">
                                    {!! $page->content !!}
                                </div>
                            </div>
                        </article>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
