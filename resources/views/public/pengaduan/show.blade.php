@extends('public.layouts.optilux')

@section('title', 'Pengaduan: '.$kategori->name)

@section('content')
    @include('public.partials.optilux.subheader', [
        'title' => $kategori->name,
        'breadcrumbs' => [
            ['label' => 'Pengaduan', 'url' => route('public.pengaduan.index')],
            ['label' => $kategori->name],
        ],
    ])

    <section>
        <div class="container">
            <div class="row g-4 gx-5">
                <div class="col-lg-3">
                    <div class="me-lg-3">
                        @foreach ($kategoris as $item)
                            <a
                                href="{{ route('public.pengaduan.show', $item->slug) }}"
                                @class([
                                    'd-block p-3 px-4 rounded-10px mb-3 relative',
                                    'bg-color text-light' => $item->id === $kategori->id,
                                    'bg-light' => $item->id !== $kategori->id,
                                ])
                            >
                                <h5 class="mb-0">{{ $item->name }}</h5>
                            </a>
                        @endforeach
                        <a href="{{ route('public.pengaduan.check-status') }}" class="btn-main fx-slide w-100 mt-2"><span>Cek Status</span></a>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="mb-4">
                        <h2 class="mb-3 kecamatan-section-title">{{ $kategori->name }}</h2>
                        @if ($kategori->description)
                            <p>{{ $kategori->description }}</p>
                        @endif
                    </div>

                    <div class="bg-light p-4 rounded-1">
                        <h4 class="mb-4">Formulir Pengaduan</h4>
                        <form method="post" action="{{ route('public.pengaduan.store', $kategori->slug) }}">
                            @csrf
                            <div class="field-set mb-3">
                                <label class="fw-semibold mb-1">Nama *</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required maxlength="255">
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field-set mb-3">
                                <label class="fw-semibold mb-1">Nomor HP *</label>
                                <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" required maxlength="30" placeholder="08xxxxxxxxxx">
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field-set mb-4">
                                <label class="fw-semibold mb-1">Isi Pengaduan *</label>
                                <textarea name="isi" class="form-control @error('isi') is-invalid @enderror" rows="6" required maxlength="5000">{{ old('isi') }}</textarea>
                                @error('isi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn-main w-100"><span>Kirim Pengaduan</span></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
