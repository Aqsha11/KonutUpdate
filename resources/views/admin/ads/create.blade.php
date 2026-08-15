@extends('admin.layouts.app')
@section('title', 'Tambah Iklan')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Iklan</h1>
        <p class="page-subtitle">Buat iklan baru</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.ads.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="link" class="form-label">URL (Opsional)</label>
                    <input type="url" name="link" id="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link') }}" placeholder="https://example.com">
                    @error('link')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">URL tujuan saat iklan diklik. Kosongkan jika tanpa tautan.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Thumbnail <span class="required">*</span></label>
                    <div class="dropzone-admin" id="dropzone">
                        <div class="dropzone-icon">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <div class="dropzone-text">Seret gambar ke sini atau klik untuk memilih</div>
                        <div class="dropzone-hint">Format: JPEG, PNG, WebP. Maks 2MB</div>
                        <input type="file" name="image" id="image" class="d-none" accept="image/*" required>
                    </div>
                    <div id="imagePreview" class="mt-2"></div>
                    @error('image')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="bi bi-save"></i> Simpan Iklan
                </button>
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
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
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
                    `;
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
