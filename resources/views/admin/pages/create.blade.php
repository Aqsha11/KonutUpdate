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
            <label for="content" class="form-label">Konten</label>
            <textarea name="content" id="content" rows="15" class="form-control @error('content') is-invalid @enderror" placeholder="Tulis konten halaman di sini...">{{ old('content') }}</textarea>
            @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
    textarea#content { font-family: var(--font-family); font-size: 15px; line-height: 1.6; }
</style>
@endpush
