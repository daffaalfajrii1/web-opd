@extends('public.layouts.clinicom')

@section('title', 'Cek Status Pengajuan')

@section('content')
    @include('public.partials.clinicom.subheader', [
        'title' => 'Cek Status Pengajuan',
        'breadcrumbs' => [
            ['label' => 'Layanan', 'url' => route('public.layanan.index')],
            ['label' => 'Cek Status'],
        ],
    ])

    <section>
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-6">
                    <div class="bg-light p-4 rounded-1">
                        <h4 class="mb-3">Masukkan Nomor Registrasi</h4>
                        <form method="post" action="{{ route('public.layanan.lookup-status') }}">
                            @csrf
                            <div class="field-set mb-3">
                                <input
                                    type="text"
                                    name="nomor"
                                    class="form-control @error('nomor') is-invalid @enderror"
                                    placeholder="Contoh: LYN0001-2026-000001"
                                    value="{{ old('nomor', request('nomor')) }}"
                                    required
                                >
                                @error('nomor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn-main w-100"><span>Cek Status</span></button>
                        </form>
                    </div>
                </div>

                @if (request()->filled('nomor'))
                    <div class="col-lg-8">
                        @if ($pengajuan)
                            <div class="bg-white p-4 rounded-1 shadow-soft">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                                    <div>
                                        <h4 class="mb-1">{{ $pengajuan->layanan->title }}</h4>
                                        <div class="text-muted">{{ $pengajuan->registration_number }}</div>
                                    </div>
                                    <span @class([
                                        'kecamatan-status-badge',
                                        'kecamatan-status-badge--pending' => $pengajuan->status === \App\Models\LayananPengajuan::STATUS_PENDING,
                                        'kecamatan-status-badge--processing' => $pengajuan->status === \App\Models\LayananPengajuan::STATUS_PROCESSING,
                                        'kecamatan-status-badge--completed' => $pengajuan->status === \App\Models\LayananPengajuan::STATUS_COMPLETED,
                                        'kecamatan-status-badge--rejected' => $pengajuan->status === \App\Models\LayananPengajuan::STATUS_REJECTED,
                                    ])>{{ $pengajuan->statusLabel() }}</span>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <strong>Tanggal Pengajuan</strong><br>
                                        {{ $pengajuan->submitted_at?->translatedFormat('d F Y H:i') ?? '—' }}
                                    </div>
                                    @if ($pengajuan->processed_at)
                                        <div class="col-md-6">
                                            <strong>Terakhir Diproses</strong><br>
                                            {{ $pengajuan->processed_at->translatedFormat('d F Y H:i') }}
                                        </div>
                                    @endif
                                </div>

                                <h5 class="mb-3">Data Pengajuan</h5>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered">
                                        <tbody>
                                            @foreach ($pengajuan->layanan->fields as $field)
                                                <tr>
                                                    <th style="width:35%">{{ $field->name }}</th>
                                                    <td>
                                                        @if ($field->isFileType() && $pengajuan->fileUrl($field->field_key))
                                                            <a href="{{ $pengajuan->fileUrl($field->field_key) }}" target="_blank" rel="noopener">Lihat file</a>
                                                        @else
                                                            {{ $pengajuan->displayValueForField($field) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if ($pengajuan->status === \App\Models\LayananPengajuan::STATUS_COMPLETED)
                                    <div class="alert alert-success">
                                        <strong>Penyelesaian:</strong> {{ $pengajuan->completionMethodLabel() }}
                                        @if ($pengajuan->completion_message)
                                            <div class="mt-2">{{ $pengajuan->completion_message }}</div>
                                        @endif
                                        @if ($pengajuan->hasUploadedResult())
                                            <div class="mt-3">
                                                <a href="{{ $pengajuan->resultFileUrl() }}" class="btn-main fx-slide" target="_blank" rel="noopener">
                                                    <span>Unduh Hasil Layanan</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @elseif ($pengajuan->status === \App\Models\LayananPengajuan::STATUS_REJECTED)
                                    <div class="alert alert-danger">
                                        Pengajuan ditolak. Silakan hubungi kantor untuk informasi lebih lanjut.
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                Nomor registrasi <strong>{{ request('nomor') }}</strong> tidak ditemukan. Periksa kembali nomor Anda.
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
