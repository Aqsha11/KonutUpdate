@extends('admin.layouts.app')
@section('title', 'Tambah Kecamatan')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Kecamatan</h1>
        <p class="page-subtitle">Buat kecamatan baru</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.kecamatans.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.kecamatans.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Nama Kecamatan <span class="required">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
            <div class="form-text">Contoh: "Pusat pemerintahan kabupaten" atau "Kecamatan dengan wilayah terluas"</div>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="sort_order" class="form-label">Urutan</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" min="0">
            @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-save"></i> Simpan
        </button>
    </form>
</div>
@endsection
