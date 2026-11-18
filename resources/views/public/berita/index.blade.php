@extends('public.layouts.clinicom')

@section('title', 'Berita')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Berita',
        'breadcrumbs' => [['label' => 'Berita']],
    ])

    <section class="rsud-strip rsud-strip--soft rsud-article-list">
        <div class="container">
            <form method="get" class="rsud-filter-bar row">
                <div class="col-md-5 mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari berita..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <select name="kategori" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('kategori') === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-gradient btn-block">Filter</button>
                </div>
            </form>

            <div class="row">
                @forelse ($beritas as $berita)
                    <div class="col-lg-3 col-md-6 mb-4">
                        @include('public.partials.clinicom.berita-card', ['berita' => $berita])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="rsud-empty">
                            <p class="mb-0">Belum ada berita yang dipublikasikan.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($beritas->hasPages())
                <div class="text-center mt-4">
                    {{ $beritas->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
