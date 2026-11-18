@extends('public.layouts.clinicom')

@section('title', 'Layanan Publik')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Layanan Publik',
        'breadcrumbs' => [['label' => 'Layanan']],
    ])

    <section class="rsud-strip rsud-strip--soft">
        <div class="container">
            <div class="row">
                @forelse ($layanans as $index => $layanan)
                    <div class="col-lg-4 col-md-6 mb-4">
                        @include('public.partials.clinicom.layanan-card', ['layanan' => $layanan, 'index' => $index])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="rsud-empty">
                            <p class="mb-0">Belum ada layanan publik yang aktif.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rsud-strip rsud-strip--teal rsud-status-cta">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <p class="rsud-kicker rsud-kicker--light mb-2">Lacak Pengajuan</p>
                    <h2 class="rsud-title rsud-title--light mb-0">Sudah mengajukan layanan? Cek status pengajuan Anda.</h2>
                </div>
                <div class="col-lg-4 text-lg-right">
                    <a class="btn rsud-btn-solid-light btn-block" href="{{ route('public.layanan.check-status') }}">
                        Cek Status
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
