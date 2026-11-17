@extends('public.layouts.optilux')

@section('title', 'Pengaduan')

@section('content')
    @include('public.partials.optilux.subheader', [
        'title' => 'Pengaduan',
        'breadcrumbs' => [
            ['label' => 'Pengaduan'],
        ],
    ])

    <section>
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <h2 class="kecamatan-section-title mb-2">Sampaikan Pengaduan Anda</h2>
                    <p class="mb-0">Pilih kategori pengaduan, isi formulir, lalu simpan nomor unik untuk memantau dan membalas feedback.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('public.pengaduan.check-status') }}" class="btn-main fx-slide"><span>Cek Status Pengaduan</span></a>
                </div>
            </div>

            <div class="row g-4">
                @forelse ($kategoris as $kategori)
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('public.pengaduan.show', $kategori->slug) }}" class="d-block bg-light p-4 rounded-1 h-100 text-decoration-none">
                            <h4 class="mb-2">{{ $kategori->name }}</h4>
                            @if ($kategori->description)
                                <p class="text-muted mb-0">{{ \Illuminate\Support\Str::limit($kategori->description, 120) }}</p>
                            @else
                                <p class="text-muted mb-0">Kirim pengaduan untuk kategori ini.</p>
                            @endif
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning mb-0">Belum ada kategori pengaduan. Silakan hubungi admin.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
