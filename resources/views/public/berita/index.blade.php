@extends('public.layouts.clinicom')

@section('title', 'Berita')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Berita',
        'breadcrumbs' => [['label' => 'Berita']],
    ])

    <section class="rsud-content-block">
        <div class="container">
            <form method="get" class="row mb-5">
                <div class="col-md-4 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari berita..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 mb-2">
                    <select name="kategori" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('kategori') === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <button type="submit" class="btn btn-gradient btn-block">Filter</button>
                </div>
            </form>

            <div class="row rsud-gap">
                @forelse ($beritas as $berita)
                    <div class="col-lg-4 col-md-6">
                        @include('public.partials.clinicom.berita-card', ['berita' => $berita])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="bg-light p-5 text-center">
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
