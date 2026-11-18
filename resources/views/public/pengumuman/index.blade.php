@extends('public.layouts.clinicom')

@section('title', 'Pengumuman')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Pengumuman',
        'breadcrumbs' => [['label' => 'Pengumuman']],
    ])

    <section class="rsud-strip rsud-strip--soft rsud-article-list">
        <div class="container">
            <div class="row">
                @forelse ($pengumumans as $pengumuman)
                    <div class="col-lg-4 col-md-6 mb-4">
                        @include('public.partials.clinicom.pengumuman-card', ['pengumuman' => $pengumuman])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="rsud-empty">
                            <p class="mb-0">Belum ada pengumuman aktif.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($pengumumans->hasPages())
                <div class="text-center mt-3">
                    {{ $pengumumans->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
