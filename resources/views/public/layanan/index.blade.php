@extends('public.layouts.clinicom')

@section('title', 'Layanan Publik')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Layanan Publik',
        'breadcrumbs' => [['label' => 'Layanan']],
    ])

    <section>
        <div class="container">
            <div class="row g-4">
                @forelse ($layanans as $index => $layanan)
                    <div class="col-lg-4 col-md-6">
                        @include('public.partials.optilux.layanan-card', ['layanan' => $layanan, 'index' => $index])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="bg-light p-5 rounded-1 text-center">
                            <p class="mb-0">Belum ada layanan publik yang aktif.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-color relative text-light pt-50 pb-50">
        <div class="container relative z-2">
            <div class="row g-4 align-items-center">
                <div class="col-md-9">
                    <h3 class="mb-0 fs-32">Sudah mengajukan layanan? Cek status pengajuan Anda.</h3>
                </div>
                <div class="col-lg-3 text-lg-end">
                    <a class="btn-main btn-line fx-slide" href="{{ route('public.layanan.check-status') }}"><span>Cek Status</span></a>
                </div>
            </div>
        </div>
    </section>
@endsection
