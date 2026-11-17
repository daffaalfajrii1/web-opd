@php $item = $category ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Nama Kategori *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $item?->name) }}" required>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Urutan</label>
        <input type="number" name="sort_order" class="form-control" min="0" max="9999" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="pengaduan-category-active" @checked(old('is_active', $item?->is_active ?? true))>
            <label class="form-check-label" for="pengaduan-category-active">Aktif</label>
        </div>
    </div>
    <div class="col-12">
        <label class="fw-semibold">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3" maxlength="1000">{{ old('description', $item?->description) }}</textarea>
        <div class="form-text">Opsional, maks. 1000 karakter.</div>
    </div>
    @if ($item)
        <div class="col-12">
            <div class="text-muted fs-12">Slug: <code>{{ $item->slug }}</code> · {{ $item->pengaduans_count ?? $item->pengaduans()->count() }} pengaduan</div>
        </div>
    @endif
</div>
