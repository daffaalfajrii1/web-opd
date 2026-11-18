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
        <label class="fw-semibold">Gambar Utama / Thumbnail</label>
        <input type="file" id="berita-thumb-source" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
        <input type="file" name="thumbnail" id="berita-thumb-upload" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="form-text">Potong ke rasio 16:9 agar ukuran sama di beranda &amp; daftar berita. Maks. 2 MB.</div>

        <div class="mt-3 border rounded p-2 bg-light" id="berita-cropper-wrap" style="display:none;">
            <div class="ratio ratio-16x9">
                <img id="berita-cropper-image" src="#" alt="Pratinjau potong" class="w-100">
            </div>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-sm btn-primary" id="berita-crop-apply">Gunakan Potongan</button>
                <button type="button" class="btn btn-sm btn-light" id="berita-crop-cancel">Batal</button>
            </div>
        </div>

        <div class="mt-3" id="berita-thumb-preview-wrap">
            @if ($item?->thumbnail)
                <img src="{{ $item->thumbnailUrl() }}" alt="Thumbnail" id="berita-thumb-preview" class="rounded border" style="width:100%;max-width:360px;aspect-ratio:16/9;object-fit:cover;">
                <div class="form-check mt-2">
                    <input type="checkbox" name="remove_thumbnail" value="1" class="form-check-input" id="remove-thumbnail">
                    <label class="form-check-label" for="remove-thumbnail">Hapus gambar utama</label>
                </div>
            @else
                <img src="#" alt="" id="berita-thumb-preview" class="rounded border d-none" style="width:100%;max-width:360px;aspect-ratio:16/9;object-fit:cover;">
            @endif
        </div>
        @error('thumbnail')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
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

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    @endpush
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
        (function () {
            const sourceInput = document.getElementById('berita-thumb-source');
            const uploadInput = document.getElementById('berita-thumb-upload');
            const wrap = document.getElementById('berita-cropper-wrap');
            const image = document.getElementById('berita-cropper-image');
            const preview = document.getElementById('berita-thumb-preview');
            const applyBtn = document.getElementById('berita-crop-apply');
            const cancelBtn = document.getElementById('berita-crop-cancel');
            let cropper = null;

            function destroyCropper() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }

            sourceInput?.addEventListener('change', function () {
                const file = sourceInput.files?.[0];
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    destroyCropper();
                    image.src = event.target.result;
                    wrap.style.display = '';
                    cropper = new Cropper(image, {
                        aspectRatio: 16 / 9,
                        viewMode: 1,
                        autoCropArea: 1,
                    });
                };
                reader.readAsDataURL(file);
            });

            cancelBtn?.addEventListener('click', function () {
                destroyCropper();
                wrap.style.display = 'none';
                sourceInput.value = '';
            });

            applyBtn?.addEventListener('click', function () {
                if (!cropper) {
                    return;
                }

                const canvas = cropper.getCroppedCanvas({
                    width: 1200,
                    height: 675,
                    imageSmoothingQuality: 'high',
                });

                canvas.toBlob(function (blob) {
                    if (!blob || !uploadInput) {
                        return;
                    }

                    const croppedFile = new File([blob], 'berita-thumb.jpg', { type: 'image/jpeg', lastModified: Date.now() });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    uploadInput.files = dataTransfer.files;

                    preview.src = canvas.toDataURL('image/jpeg');
                    preview.classList.remove('d-none');

                    destroyCropper();
                    wrap.style.display = 'none';
                    sourceInput.value = '';
                }, 'image/jpeg', 0.9);
            });
        })();
        </script>
    @endpush
@endonce
