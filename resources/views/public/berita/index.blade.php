@extends('public.layouts.optilux')

@section('title', 'Berita')

@section('content')
    @include('public.partials.optilux.subheader', [
        'title' => 'Berita',
        'breadcrumbs' => [['label' => 'Berita']],
    ])

    <section>
        <div class="container">
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari berita..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="kategori" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('kategori') === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn-main w-100"><span>Filter</span></button>
                </div>
            </form>

            <div class="row g-4">
                @forelse ($beritas as $berita)
                    <div class="col-lg-4 col-md-6">
                        @include('public.partials.optilux.berita-card', ['berita' => $berita])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="bg-light p-5 rounded-1 text-center">
                            <p class="mb-0">Belum ada berita yang dipublikasikan.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($beritas->hasPages())
                <div class="kecamatan-pagination-wrap">
                    {{ $beritas->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
