@php $item = $dokter ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Nama Dokter *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $item?->name) }}" required>
        @error('name')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Spesialisasi *</label>
        @if (($spesialisasis ?? collect())->isEmpty())
            <div class="alert alert-warning mb-2 py-2">
                Belum ada spesialisasi. <a href="{{ route('admin.dokter-spesialisasi.create') }}" class="fw-semibold">Tambah spesialisasi dulu</a> sebelum menyimpan dokter.
            </div>
            <select name="dokter_spesialisasi_id" class="form-select" required disabled>
                <option value="">— Belum ada spesialisasi —</option>
            </select>
        @else
            <select name="dokter_spesialisasi_id" class="form-select" required>
                <option value="">— Pilih spesialisasi —</option>
                @foreach ($spesialisasis as $spesialisasi)
                    <option value="{{ $spesialisasi->id }}" @selected(old('dokter_spesialisasi_id', $item?->dokter_spesialisasi_id) == $spesialisasi->id)>
                        {{ $spesialisasi->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">
                Kelola daftar di
                <a href="{{ route('admin.dokter-spesialisasi.index') }}">Spesialisasi Dokter</a>.
            </div>
        @endif
        @error('dokter_spesialisasi_id')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Telepon</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $item?->phone) }}" placeholder="Contoh: 0821xxxxxxx">
        @error('phone')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $item?->email) }}" placeholder="opsional">
        @error('email')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Poli / Unit</label>
        <input type="text" name="poli" class="form-control" value="{{ old('poli', $item?->poli) }}" placeholder="Contoh: Poli Anak">
        @error('poli')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="fw-semibold">Facebook</label>
        <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $item?->facebook_url) }}" placeholder="https://facebook.com/...">
        @error('facebook_url')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Foto Dokter</label>
        <input type="file" id="dokter-photo-source" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
        <input type="file" name="photo" id="dokter-photo-upload" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="form-text">Pilih foto lalu potong area tampilan. Maks. 2 MB.</div>

        <div class="mt-3 border rounded p-2 bg-light" id="dokter-cropper-wrap" style="display:none;">
            <div class="ratio ratio-4x3">
                <img id="dokter-cropper-image" src="#" alt="Pratinjau potong" class="w-100">
            </div>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-sm btn-primary" id="dokter-crop-apply">Gunakan Potongan</button>
                <button type="button" class="btn btn-sm btn-light" id="dokter-crop-cancel">Batal</button>
            </div>
        </div>

        <div class="mt-3" id="dokter-photo-preview-wrap">
            @if ($item?->photoUrl())
                <img src="{{ $item->photoUrl() }}" alt="{{ $item->name }}" id="dokter-photo-preview" class="rounded border" style="max-height:200px;object-fit:cover;">
                <div class="form-check mt-2">
                    <input type="checkbox" name="remove_photo" value="1" class="form-check-input" id="remove-dokter-photo">
                    <label class="form-check-label" for="remove-dokter-photo">Hapus foto</label>
                </div>
            @else
                <img src="#" alt="" id="dokter-photo-preview" class="rounded border d-none" style="max-height:200px;object-fit:cover;">
            @endif
        </div>
        @error('photo')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Urutan Tampil</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
        <div class="form-check form-switch mt-4">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="dokter-active" @checked(old('is_active', $item?->is_active ?? true))>
            <label class="form-check-label" for="dokter-active">Tampilkan di website publik</label>
        </div>
        @if ($item)
            <div class="text-muted fs-12 mt-3">Slug: <code>{{ $item->slug }}</code></div>
        @endif
    </div>
    <div class="col-12">
        <label class="fw-semibold">Bio / Profil Singkat</label>
        <textarea name="bio" class="form-control" rows="4">{{ old('bio', $item?->bio) }}</textarea>
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
            const sourceInput = document.getElementById('dokter-photo-source');
            const uploadInput = document.getElementById('dokter-photo-upload');
            const wrap = document.getElementById('dokter-cropper-wrap');
            const image = document.getElementById('dokter-cropper-image');
            const preview = document.getElementById('dokter-photo-preview');
            const applyBtn = document.getElementById('dokter-crop-apply');
            const cancelBtn = document.getElementById('dokter-crop-cancel');
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
                        aspectRatio: 4 / 5,
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
                    width: 800,
                    height: 1000,
                    imageSmoothingQuality: 'high',
                });

                canvas.toBlob(function (blob) {
                    if (!blob || !uploadInput) {
                        return;
                    }

                    const croppedFile = new File([blob], 'dokter-photo.jpg', { type: 'image/jpeg', lastModified: Date.now() });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    uploadInput.files = dataTransfer.files;

                    preview.src = canvas.toDataURL('image/jpeg');
                    preview.classList.remove('d-none');

                    destroyCropper();
                    wrap.style.display = 'none';
                    sourceInput.value = '';
                }, 'image/jpeg', 0.92);
            });
        })();
        </script>
    @endpush
@endonce