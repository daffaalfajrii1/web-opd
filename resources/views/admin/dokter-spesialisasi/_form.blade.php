@php $item = $spesialisasi ?? null; @endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Nama Spesialisasi *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $item?->name) }}" required placeholder="Contoh: Spesialis Anak">
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Urutan</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="spesialisasi-active" @checked(old('is_active', $item?->is_active ?? true))>
            <label class="form-check-label" for="spesialisasi-active">Aktif</label>
        </div>
    </div>
</div>
