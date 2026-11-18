@extends('public.layouts.clinicom')

@section('title', 'Galeri')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Galeri',
        'breadcrumbs' => [['label' => 'Galeri']],
    ])

    <section class="rsud-strip rsud-strip--soft rsud-article-list">
        <div class="container">
            <div class="row">
                @forelse ($galeris as $galeri)
                    <div class="col-lg-4 col-md-6 mb-4">
                        @include('public.partials.clinicom.galeri-album-card', ['galeri' => $galeri])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="rsud-empty">
                            <p class="mb-0">Belum ada galeri yang dipublikasikan.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($galeris->hasPages())
                <div class="text-center mt-3">
                    {{ $galeris->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
