@extends('public.layouts.clinicom')

@section('title', $layanan->title)

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => $layanan->title,
        'breadcrumbs' => [
            ['label' => 'Layanan', 'url' => route('public.layanan.index')],
            ['label' => $layanan->title],
        ],
    ])

    <section class="rsud-strip rsud-strip--soft">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <aside class="rsud-article__sidebar">
                        <h4>Daftar Layanan</h4>
                        <div class="rsud-service-nav">
                            @foreach ($allLayanans as $item)
                                <a
                                    href="{{ route('public.layanan.show', $item->slug) }}"
                                    @class(['rsud-service-nav__item', 'is-active' => $item->id === $layanan->id])
                                >
                                    {{ $item->title }}
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('public.layanan.check-status') }}" class="btn rsud-btn-outline btn-block mt-3">
                            Cek Status
                        </a>
                    </aside>
                </div>

                <div class="col-lg-9">
                    <div class="rsud-article__card">
                        <div class="rsud-article__body">
                            <small class="rsud-service-code">{{ $layanan->code }}</small>
                            <h2 class="rsud-title mb-3">{{ $layanan->title }}</h2>
                            @if ($layanan->description)
                                <p class="rsud-lead">{{ $layanan->description }}</p>
                            @endif

                            @if ($layanan->fields->isEmpty())
                                <div class="alert alert-warning mb-0">Formulir layanan belum dikonfigurasi. Silakan hubungi kantor.</div>
                            @else
                                <div class="rsud-layanan-form">
                                    <h3 class="rsud-layanan-form__title">Formulir Pengajuan</h3>
                                    <form method="post" action="{{ route('public.layanan.store', $layanan->slug) }}" enctype="multipart/form-data">
                                        @csrf
                                        @foreach ($layanan->fields as $field)
                                            @include('public.partials.optilux.field-input', ['field' => $field])
                                        @endforeach
                                        <button type="submit" class="btn btn-gradient btn-block">Kirim Pengajuan</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
