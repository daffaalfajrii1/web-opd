@extends('admin.layouts.app')

@section('title', 'Edit Dokter')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Dokter',
        'crumbs' => [
            ['label' => 'Dokter', 'url' => route('admin.dokter.index')],
            ['label' => $dokter->name],
        ],
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.dokter.update', $dokter) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.dokter._form', ['dokter' => $dokter])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.dokter.index') }}" class="btn btn-light">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="mb-0">Jadwal Praktik</h6>
                <span class="text-muted fs-12">{{ $dokter->jadwals->count() }} jadwal</span>
            </div>
            <div class="card-body">
                @forelse($dokter->jadwals as $jadwal)
                    <div class="rsud-jadwal-item border rounded-3 mb-3 p-3">
                        <form method="POST" action="{{ route('admin.dokter.jadwal.update', [$dokter, $jadwal]) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-2 align-items-end">
                                <div class="col-6 col-md-2">
                                    <label class="fw-semibold fs-12 mb-1">Hari</label>
                                    <select name="day_of_week" class="form-select form-select-sm" required>
                                        @foreach ($dayOptions as $dayValue => $dayLabel)
                                            <option value="{{ $dayValue }}" @selected($jadwal->day_of_week == $dayValue)>{{ $dayLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="fw-semibold fs-12 mb-1">Mulai</label>
                                    <input type="time" name="start_time" class="form-control form-control-sm" value="{{ $jadwal->formatStartTime() }}" required>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="fw-semibold fs-12 mb-1">Selesai</label>
                                    <input type="time" name="end_time" class="form-control form-control-sm" value="{{ $jadwal->formatEndTime() }}" required>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="fw-semibold fs-12 mb-1">Catatan</label>
                                    <input type="text" name="note" class="form-control form-control-sm" value="{{ $jadwal->note }}" maxlength="255" placeholder="Opsional">
                                </div>
                                <div class="col-6 col-md-1">
                                    <label class="fw-semibold fs-12 mb-1 d-block">Status</label>
                                    <div class="form-check form-switch mt-1">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="jadwal-active-{{ $jadwal->id }}" @checked($jadwal->is_active)>
                                        <label class="form-check-label fs-12" for="jadwal-active-{{ $jadwal->id }}">Aktif</label>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2">
                                    <div class="d-flex gap-1 justify-content-md-end">
                                        <button type="submit" class="btn btn-sm btn-primary px-2" title="Simpan">
                                            <i class="feather-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.dokter.jadwal.destroy', [$dokter, $jadwal]) }}" class="mt-2 text-end" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Hapus">
                                <i class="feather-trash-2"></i>
                                <span class="ms-1">Hapus</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted mb-4">Belum ada jadwal praktik.</p>
                @endforelse

                <div class="border-top pt-3 mt-2">
                    <h6 class="fw-semibold mb-3">Tambah Jadwal</h6>
                    <form method="POST" action="{{ route('admin.dokter.jadwal.store', $dokter) }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-3">
                            <label class="fw-semibold fs-13">Hari</label>
                            <select name="day_of_week" class="form-select form-select-sm" required>
                                <option value="">Pilih hari</option>
                                @foreach ($dayOptions as $dayValue => $dayLabel)
                                    <option value="{{ $dayValue }}" @selected((int) old('day_of_week') === $dayValue)>{{ $dayLabel }}</option>
                                @endforeach
                            </select>
                            @error('day_of_week')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2">
                            <label class="fw-semibold fs-13">Mulai</label>
                            <input type="time" name="start_time" class="form-control form-control-sm" value="{{ old('start_time') }}" required>
                            @error('start_time')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2">
                            <label class="fw-semibold fs-13">Selesai</label>
                            <input type="time" name="end_time" class="form-control form-control-sm" value="{{ old('end_time') }}" required>
                            @error('end_time')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="fw-semibold fs-13">Catatan</label>
                            <input type="text" name="note" class="form-control form-control-sm" value="{{ old('note') }}" maxlength="255" placeholder="Opsional">
                        </div>
                        <div class="col-md-1">
                            <div class="form-check form-switch mb-1">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="jadwal-baru-active" @checked(old('is_active', true))>
                                <label class="form-check-label fs-12" for="jadwal-baru-active">Aktif</label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
