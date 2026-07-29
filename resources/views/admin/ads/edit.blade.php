@extends('admin.layouts.app')
@section('title', 'Edit Iklan')
@section('content')
<div class="page-header">
    <div>
        <h1>Edit Iklan</h1>
        <p class="page-subtitle">Perbarui iklan yang sudah ada</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.ads.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="title" class="form-label">Judul Iklan <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Judul Iklan" value="{{ old('title', $ad->title) }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="link" class="form-label">Tautan</label>
                    <input type="url" name="link" id="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $ad->link) }}" placeholder="https://example.com">
                    @error('link')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">URL tujuan saat iklan diklik</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Gambar Iklan</label>
                    <div class="dropzone-admin" id="dropzone">
                        <div class="dropzone-icon">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <div class="dropzone-text">Seret gambar baru ke sini atau klik untuk mengganti</div>
                        <div class="dropzone-hint">Format: JPEG, PNG, WebP. Maks 2MB. Kosongkan jika tidak ingin mengganti</div>
                        <input type="file" name="image" id="image" class="d-none" accept="image/*">
                    </div>
                    <div id="imagePreview" class="mt-2">
                        @if($ad->image)
                        <div class="dropzone-preview" id="currentImage">
                            <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}">
                            <div>
                                <div class="file-name">Gambar saat ini</div>
                                <div class="file-size">{{ $ad->title }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @error('image')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="position" class="form-label">Posisi <span class="required">*</span></label>
                    <select name="position" id="position" class="form-select @error('position') is-invalid @enderror">
                        <option value="sidebar_top" {{ old('position', $ad->position) === 'sidebar_top' ? 'selected' : '' }}>Sidebar Atas</option>
                        <option value="sidebar_bottom" {{ old('position', $ad->position) === 'sidebar_bottom' ? 'selected' : '' }}>Sidebar Bawah</option>
                        <option value="in_article" {{ old('position', $ad->position) === 'in_article' ? 'selected' : '' }}>Dalam Artikel</option>
                    </select>
                    @error('position')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="sort_order" class="form-label">Urutan</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" placeholder="Urutan" value="{{ old('sort_order', $ad->sort_order) }}" min="0">
                    <div class="form-text">Semakin kecil semakin atas</div>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $ad->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Aktif</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="starts_at" class="form-label">Tanggal Mulai</label>
                    <input type="date" name="starts_at" id="starts_at" class="form-control" placeholder="Tanggal Mulai" value="{{ old('starts_at', $ad->starts_at ? $ad->starts_at->format('Y-m-d') : '') }}">
                    <div class="form-text">Kosongkan jika tidak ada batas</div>
                </div>
                <div class="form-group">
                    <label for="ends_at" class="form-label">Tanggal Berakhir</label>
                    <input type="date" name="ends_at" id="ends_at" class="form-control" placeholder="Tanggal Berakhir" value="{{ old('ends_at', $ad->ends_at ? $ad->ends_at->format('Y-m-d') : '') }}">
                    <div class="form-text">Kosongkan jika tidak ada batas</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary flex-fill">
                        <i class="bi bi-save"></i> Perbarui
                    </button>
                    <a href="{{ route('admin.ads.index') }}" class="btn-admin btn-admin-secondary">
                        <i class="bi bi-x"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('image');
        const preview = document.getElementById('imagePreview');

        dropzone.addEventListener('click', function() { fileInput.click(); });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('currentImage')?.remove();
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.insertAdjacentHTML('beforeend', `
                        <div class="dropzone-preview">
                            <img src="${e.target.result}" alt="Preview">
                            <div>
                                <div class="file-name">${file.name}</div>
                                <div class="file-size">${(file.size / 1024).toFixed(1)} KB</div>
                            </div>
                            <button type="button" class="ms-auto btn-action btn-action-delete" onclick="document.getElementById('image').value=''; this.closest('.dropzone-preview').remove()">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            }
        });

        ['dragenter', 'dragover'].forEach(evt => {
            dropzone.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('dragover');
            });
        });
        ['dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('dragover');
            });
        });
        dropzone.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    });
</script>
@endpush
