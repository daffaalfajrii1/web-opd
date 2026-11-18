@php $item = $link ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="fw-semibold">Nama Layanan *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item?->title) }}" required placeholder="Contoh: BPJS Kesehatan">
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Tautan / URL *</label>
        <input type="url" name="url" class="form-control" value="{{ old('url', $item?->url) }}" required placeholder="https://...">
    </div>
    <div class="col-md-8">
        <label class="fw-semibold">Deskripsi singkat</label>
        <input type="text" name="description" class="form-control" value="{{ old('description', $item?->description) }}" maxlength="255" placeholder="Opsional">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="link-layanan-active" @checked(old('is_active', $item?->is_active ?? true))>
            <label class="form-check-label" for="link-layanan-active">Tampil di beranda</label>
        </div>
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Logo</label>
        <input type="file" name="logo" class="form-control" accept="image/*">
        <small class="text-muted">PNG/JPG, maks 2MB. Disarankan kotak/transparan.</small>
    </div>
    @if ($item?->logo_path)
        <div class="col-md-6">
            <label class="fw-semibold d-block">Logo saat ini</label>
            <img src="{{ $item->logoUrl() }}" alt="{{ $item->title }}" style="max-height:72px;object-fit:contain;background:#f5f7f8;padding:8px;border-radius:8px">
            <div class="form-check mt-2">
                <input type="checkbox" name="remove_logo" value="1" class="form-check-input" id="remove-logo">
                <label class="form-check-label" for="remove-logo">Hapus logo</label>
            </div>
        </div>
    @endif
</div>
