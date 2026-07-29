@extends('admin.layouts.app')
@section('title', 'Tambah Permission')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Permission</h1>
        <p class="page-subtitle">Buat hak akses baru</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.permissions.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.permissions.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label">Nama Permission <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Permission" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="slug" class="form-label">Slug <span class="required">*</span></label>
                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required placeholder="create_posts">
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Identifier unik. Contoh: <code>create_posts</code>, <code>edit_posts</code></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsi Permission (opsional)">{{ old('description') }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-save"></i> Simpan
        </button>
    </form>
</div>
@endsection
