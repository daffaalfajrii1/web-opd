@php $item = $berita ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Judul *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item?->title) }}" required>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Kategori *</label>
        <select name="berita_category_id" class="form-select" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('berita_category_id', $item?->berita_category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @if ($categories->isEmpty())
            <div class="form-text text-warning">
                Belum ada kategori aktif.
                <a href="{{ route('admin.berita-kategori.create') }}">Tambah kategori</a>
            </div>
        @endif
    </div>
    <div class="col-12">
        <label class="fw-semibold">Ringkasan</label>
        <textarea name="excerpt" class="form-control" rows="2" maxlength="500">{{ old('excerpt', $item?->excerpt) }}</textarea>
        <div class="form-text">Tampil di daftar berita (opsional, maks. 500 karakter).</div>
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Gambar Utama</label>
        <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="form-text">JPG, PNG, GIF, atau WebP. Maks. 2 MB.</div>
        @if ($item?->thumbnail)
            <div class="mt-2">
                <img src="{{ $item->thumbnailUrl() }}" alt="Thumbnail" class="rounded border" style="max-height:120px">
                <div class="form-check mt-2">
                    <input type="checkbox" name="remove_thumbnail" value="1" class="form-check-input" id="remove-thumbnail">
                    <label class="form-check-label" for="remove-thumbnail">Hapus gambar utama</label>
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Status *</label>
        <select name="status" class="form-select" required>
            <option value="{{ App\Models\Berita::STATUS_DRAFT }}" @selected(old('status', $item?->status ?? App\Models\Berita::STATUS_DRAFT) === App\Models\Berita::STATUS_DRAFT)>
                Draft
            </option>
            <option value="{{ App\Models\Berita::STATUS_PUBLISHED }}" @selected(old('status', $item?->status) === App\Models\Berita::STATUS_PUBLISHED)>
                Terbit (Publish)
            </option>
        </select>
        <div class="form-text">Draft tidak tampil di website publik.</div>
        @if ($item)
            <div class="mt-3 p-2 bg-light rounded">
                <div class="fs-12 text-muted">Dilihat</div>
                <div class="fw-semibold">{{ number_format($item->views_count) }} kali</div>
            </div>
        @endif
    </div>
    <div class="col-12">
        <label class="fw-semibold">Konten</label>
        @include('admin.partials.richtext-editor', [
            'uploadUrl' => route('admin.berita.editor-upload'),
            'formId' => 'berita-form',
            'textareaId' => 'berita-content',
            'statusId' => 'berita-editor-status',
            'content' => old('content', $item?->content),
        ])
        <div class="form-text">Gunakan editor untuk format teks, sisipkan gambar, tabel, dan daftar.</div>
    </div>
</div>
