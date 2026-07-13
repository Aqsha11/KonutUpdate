@extends('admin.layouts.app')
@section('title', 'Edit Tag')
@section('content')
<div class="page-header">
    <div>
        <h1>Edit Tag</h1>
        <p class="page-subtitle">Perbarui tag berita</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.tags.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name" class="form-label">Nama Tag <span class="required">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tag->name) }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-save"></i> Update
        </button>
    </form>
</div>
@endsection
