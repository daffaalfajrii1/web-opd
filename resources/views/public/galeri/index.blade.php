@extends('public.layouts.clinicom')

@section('title', 'Galeri')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Galeri',
        'breadcrumbs' => [['label' => 'Galeri']],
    ])

    <section>
        <div class="container">
            <div class="row g-4">
                @forelse ($galeris as $galeri)
                    <div class="col-lg-4 col-md-6">
                        @include('public.partials.optilux.galeri-album-card', ['galeri' => $galeri])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="bg-light p-5 rounded-1 text-center">
                            <p class="mb-0">Belum ada galeri yang dipublikasikan.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($galeris->hasPages())
                <div class="kecamatan-pagination-wrap">
                    {{ $galeris->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
