@extends('public.layouts.clinicom')

@section('title', 'Pengumuman')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Pengumuman',
        'breadcrumbs' => [['label' => 'Pengumuman']],
    ])

    <section>
        <div class="container">
            <div class="row g-4">
                @forelse ($pengumumans as $pengumuman)
                    <div class="col-lg-4 col-md-6">
                        <div class="bg-white p-4 rounded-1 h-100 shadow-soft hover">
                            <div class="mb-2">
                                @if ($pengumuman->isPenting())
                                    <span class="kecamatan-badge-penting me-1">Penting</span>
                                @endif
                                @if ($pengumuman->is_featured)
                                    <span class="kecamatan-badge-featured">Unggulan</span>
                                @endif
                            </div>
                            <h4 class="mb-2"><a href="{{ route('public.pengumuman.show', $pengumuman->slug) }}" class="text-dark">{{ $pengumuman->title }}</a></h4>
                            @if ($pengumuman->published_at)
                                <small class="text-muted d-block mb-2">{{ $pengumuman->published_at->translatedFormat('d F Y') }}</small>
                            @endif
                            <p class="mb-2">{{ $pengumuman->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($pengumuman->content), 140) }}</p>
                            <small class="text-muted">{{ $pengumuman->attachmentsCountLabel() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="bg-light p-5 rounded-1 text-center">
                            <p class="mb-0">Belum ada pengumuman aktif.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($pengumumans->hasPages())
                <div class="spacer-single"></div>
                {{ $pengumumans->links() }}
            @endif
        </div>
    </section>
@endsection
