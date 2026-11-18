@php $item = $menu ?? null; @endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Judul Menu *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item?->title) }}" required>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Urutan</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="nav-menu-active" @checked(old('is_active', $item?->is_active ?? true))>
            <label class="form-check-label" for="nav-menu-active">Aktif di navbar</label>
        </div>
    </div>
</div>
