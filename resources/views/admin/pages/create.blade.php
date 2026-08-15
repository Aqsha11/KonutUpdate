@extends('admin.layouts.app')
@section('title', 'Tambah Halaman')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Halaman</h1>
        <p class="page-subtitle">Buat halaman statis baru</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.pages.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.pages.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title" class="form-label">Judul Halaman <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Judul Halaman" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="slug" class="form-label">Slug <span class="required">*</span></label>
                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required placeholder="tentang-kami">
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">URL unik untuk halaman. Contoh: <code>tentang-kami</code></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="editor" class="form-label">Konten</label>
            <textarea name="content" id="editor" rows="12" class="form-control @error('content') is-invalid @enderror" placeholder="Tulis konten halaman di sini..." style="display:none;">{{ old('content') }}</textarea>
            <div id="editor-container" style="min-height:500px;"></div>
            @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="mt-2 d-flex gap-2">
                <button type="button" class="btn-admin btn-admin-sm btn-admin-secondary" onclick="togglePreview()">
                    <i class="bi bi-eye"></i> Preview
                </button>
            </div>
            <div id="preview" class="mt-3 p-4 border rounded d-none"></div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" name="is_published" id="is_published" class="form-check-input" value="1" checked>
                <label for="is_published" class="form-check-label">Publikasikan</label>
            </div>
        </div>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-save"></i> Simpan
        </button>
    </form>
</div>
@endsection

@push('styles')
<style>
    .ck-editor__editable { min-height: 500px; }
    .ck-editor__editable a { color: #FF6B00; }
    .ck.ck-editor { border-radius: 10px; overflow: hidden; border: 1.5px solid var(--border); transition: border-color 300ms; }
    .ck.ck-editor:focus-within { border-color: #FF6B00; box-shadow: 0 0 0 3px rgba(255,107,0,0.08); }
    .ck.ck-toolbar { border: none !important; border-bottom: 1px solid var(--border) !important; background: #F8FAFC !important; }
    .ck.ck-content { border: none !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initCKEditor('editor', 'Tulis konten halaman di sini...');
    });
</script>
@endpush
