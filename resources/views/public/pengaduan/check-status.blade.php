@extends('public.layouts.optilux')

@section('title', 'Cek Status Pengaduan')

@section('content')
    @include('public.partials.optilux.subheader', [
        'title' => 'Cek Status Pengaduan',
        'breadcrumbs' => [
            ['label' => 'Pengaduan', 'url' => route('public.pengaduan.index')],
            ['label' => 'Cek Status'],
        ],
    ])

    <section>
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-6">
                    <div class="bg-light p-4 rounded-1">
                        <h4 class="mb-3">Masukkan Nomor Pengaduan</h4>
                        <p class="text-muted">Masukkan nomor pengaduan beserta nomor HP yang Anda gunakan saat mengirim, untuk memantau status dan membalas feedback.</p>
                        <form method="post" action="{{ route('public.pengaduan.lookup-status') }}">
                            @csrf
                            <div class="field-set mb-3">
                                <label class="fw-semibold mb-1">Nomor Pengaduan *</label>
                                <input
                                    type="text"
                                    name="nomor"
                                    class="form-control @error('nomor') is-invalid @enderror"
                                    placeholder="Contoh: PGD-2026-58274139"
                                    value="{{ old('nomor', $nomor) }}"
                                    required
                                >
                                @error('nomor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field-set mb-3">
                                <label class="fw-semibold mb-1">Nomor HP Pelapor *</label>
                                <input
                                    type="text"
                                    name="no_hp"
                                    class="form-control @error('no_hp') is-invalid @enderror"
                                    placeholder="08xxxxxxxxxx"
                                    value="{{ old('no_hp') }}"
                                    inputmode="numeric"
                                    autocomplete="tel"
                                    required
                                >
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn-main w-100"><span>Cek Status</span></button>
                        </form>
                    </div>
                </div>

                @if ($nomor !== '' && ! $verified)
                    <div class="col-lg-8">
                        <div class="alert alert-warning mb-0">
                            Nomor pengaduan <strong>{{ $nomor }}</strong> belum dapat ditampilkan. Pastikan nomor pengaduan dan nomor HP yang Anda masukkan sesuai dengan data saat pengaduan dikirim.
                        </div>
                    </div>
                @endif

                @if ($verified && $pengaduan)
                    <div class="col-lg-8">
                        <div class="bg-white p-4 rounded-1 shadow-soft">
                            <div class="alert alert-info">
                                <strong>Harap diingat nomor pengaduannya:</strong>
                                <code class="fs-5">{{ $pengaduan->nomor }}</code>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <h4 class="mb-1">{{ $pengaduan->kategori?->name }}</h4>
                                    <div class="text-muted">{{ $pengaduan->nomor }}</div>
                                </div>
                                <span @class([
                                    'kecamatan-status-badge',
                                    'kecamatan-status-badge--pending' => $pengaduan->status === \App\Models\Pengaduan::STATUS_BARU,
                                    'kecamatan-status-badge--processing' => $pengaduan->status === \App\Models\Pengaduan::STATUS_DIPROSES,
                                    'kecamatan-status-badge--completed' => $pengaduan->status === \App\Models\Pengaduan::STATUS_SELESAI,
                                ])>{{ $pengaduan->statusLabel() }}</span>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <strong>Nama</strong><br>{{ $pengaduan->nama }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Nomor HP</strong><br>{{ $pengaduan->maskedNoHp() }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Tanggal</strong><br>
                                    {{ $pengaduan->submitted_at?->translatedFormat('d F Y H:i') ?? '—' }}
                                </div>
                            </div>

                            <h5 class="mb-2">Isi Pengaduan</h5>
                            <div class="p-3 bg-light rounded mb-4" style="white-space: pre-wrap">{{ $pengaduan->isi }}</div>

                            <h5 class="mb-3">Feedback</h5>
                            <div class="d-flex flex-column gap-3 mb-4">
                                @forelse ($pengaduan->feedbacks as $feedback)
                                    <div @class([
                                        'p-3 rounded',
                                        'bg-color text-light' => $feedback->isAdmin(),
                                        'bg-light' => $feedback->isUser(),
                                    ])>
                                        <div class="d-flex justify-content-between gap-2 mb-2">
                                            <strong>{{ $feedback->isAdmin() ? 'Admin' : 'Anda' }}</strong>
                                            <span class="{{ $feedback->isAdmin() ? 'opacity-75' : 'text-muted' }} fs-12">{{ $feedback->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div style="white-space: pre-wrap">{{ $feedback->message }}</div>
                                    </div>
                                @empty
                                    <div class="text-muted">Belum ada feedback dari admin.</div>
                                @endforelse
                            </div>

                            <div class="bg-light p-4 rounded-1">
                                <h5 class="mb-3">Balas Feedback</h5>
                                @if ($pengaduan->awaitingAdminReply())
                                    <div class="alert alert-info d-flex align-items-start gap-2 mb-0">
                                        <i class="icofont-clock-time mt-1" aria-hidden="true"></i>
                                        <div>
                                            <strong>Balasan Anda sudah terkirim.</strong><br>
                                            Sedang menunggu jawaban admin. Anda dapat mengirim pesan baru setelah admin membalas.
                                        </div>
                                    </div>
                                @else
                                    <form method="post" action="{{ route('public.pengaduan.feedback') }}">
                                        @csrf
                                        <input type="hidden" name="nomor" value="{{ $pengaduan->nomor }}">
                                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" required maxlength="5000" placeholder="Tulis balasan Anda...">{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <button type="submit" class="btn-main w-100 mt-3"><span>Kirim Balasan</span></button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
