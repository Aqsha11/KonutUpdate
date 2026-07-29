@extends('admin.layouts.app')
@section('title', 'Tambah Kategori')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Kategori</h1>
        <p class="page-subtitle">Buat kategori berita baru</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.categories.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Nama Kategori <span class="required">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Kategori" value="{{ old('name') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Deskripsi Kategori (opsional)">{{ old('description') }}</textarea>
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
