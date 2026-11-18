@php $item = $jabatan ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Nama Jabatan *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $item?->name) }}" required>
        @error('name')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="jabatan-active" @checked(old('is_active', $item?->is_active ?? true))>
            <label class="form-check-label" for="jabatan-active">Aktif</label>
        </div>
    </div>
    <div class="col-12">
        <label class="fw-semibold">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $item?->description) }}</textarea>
    </div>
</div>
