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

    <section>
        <div class="container">
            <div class="row g-4 gx-5">
                <div class="col-lg-3">
                    <div class="me-lg-3">
                        @foreach ($allLayanans as $item)
                            <a
                                href="{{ route('public.layanan.show', $item->slug) }}"
                                @class([
                                    'd-block p-3 px-4 rounded-10px mb-3 relative',
                                    'bg-color text-light' => $item->id === $layanan->id,
                                    'bg-light' => $item->id !== $layanan->id,
                                ])
                            >
                                <h5 class="mb-0">{{ $item->title }}</h5>
                            </a>
                        @endforeach
                        <a href="{{ route('public.layanan.check-status') }}" class="btn-main fx-slide w-100 mt-2"><span>Cek Status</span></a>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="mb-4">
                        <small class="id-color">{{ $layanan->code }}</small>
                        <h2 class="mb-3 kecamatan-section-title">{{ $layanan->title }}</h2>
                        @if ($layanan->description)
                            <p>{{ $layanan->description }}</p>
                        @endif
                    </div>

                    @if ($layanan->fields->isEmpty())
                        <div class="alert alert-warning">Formulir layanan belum dikonfigurasi. Silakan hubungi kantor.</div>
                    @else
                        <div class="bg-light p-4 rounded-1 kecamatan-layanan-form">
                            <h4 class="mb-4">Formulir Pengajuan</h4>
                            <form method="post" action="{{ route('public.layanan.store', $layanan->slug) }}" enctype="multipart/form-data">
                                @csrf
                                @foreach ($layanan->fields as $field)
                                    @include('public.partials.optilux.field-input', ['field' => $field])
                                @endforeach
                                <button type="submit" class="btn-main w-100"><span>Kirim Pengajuan</span></button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
