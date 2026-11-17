@extends('admin.layouts.app')

@section('title', 'Detail Pengaduan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Detail Pengaduan',
        'crumbs' => [
            ['label' => 'Pengaduan', 'url' => route('admin.pengaduan.index')],
            ['label' => 'Detail'],
        ],
    ])

    <div class="main-content">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0">Data Pengaduan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="fs-12 text-muted">Nomor Pengaduan</div>
                            <div class="fs-20 fw-bold"><code>{{ $pengaduan->nomor }}</code></div>
                            <div class="text-muted fs-13 mt-1">
                                Kategori: <strong>{{ $pengaduan->kategori?->name }}</strong>
                            </div>
                            <div class="text-muted fs-13">
                                Diajukan: {{ $pengaduan->submitted_at?->format('d F Y H:i') }} WIB
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width:35%" class="bg-light">Nama</th>
                                        <td>{{ $pengaduan->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Nomor HP</th>
                                        <td>{{ $pengaduan->no_hp }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Status</th>
                                        <td>{{ $pengaduan->statusLabel() }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Isi Pengaduan</th>
                                        <td style="white-space: pre-wrap">{{ $pengaduan->isi }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="mb-3">Riwayat Feedback</h5>
                        <div class="d-flex flex-column gap-3 mb-4">
                            @forelse ($pengaduan->feedbacks as $feedback)
                                <div @class([
                                    'p-3 rounded border',
                                    'bg-soft-primary border-primary-subtle' => $feedback->isAdmin(),
                                    'bg-light' => $feedback->isUser(),
                                ])>
                                    <div class="d-flex justify-content-between gap-2 mb-2">
                                        <strong>{{ $feedback->isAdmin() ? 'Admin'.($feedback->admin?->name ? ' ('.$feedback->admin->name.')' : '') : 'Pelapor' }}</strong>
                                        <span class="text-muted fs-12">{{ $feedback->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div style="white-space: pre-wrap">{{ $feedback->message }}</div>
                                </div>
                            @empty
                                <div class="text-muted">Belum ada feedback.</div>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('admin.pengaduan.feedback', $pengaduan) }}">
                            @csrf
                            <label class="fw-semibold">Kirim Feedback</label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" required maxlength="5000">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <button type="submit" class="btn btn-primary mt-3">Kirim Feedback</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0">Ubah Status</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.pengaduan.status', $pengaduan) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select mb-3" required>
                                @foreach (App\Models\Pengaduan::STATUSES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $pengaduan->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-light-brand w-100">Simpan Status</button>
                        </form>
                        @if ($pengaduan->processor)
                            <div class="text-muted fs-12 mt-3">
                                Diproses oleh {{ $pengaduan->processor->name }}
                                @if ($pengaduan->processed_at)
                                    · {{ $pengaduan->processed_at->format('d/m/Y H:i') }}
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
