@extends('admin.layouts.app')
@section('title', 'Edit Video')
@section('content')
<div class="page-header">
    <div>
        <h1>Edit Video</h1>
        <p class="page-subtitle">{{ $post->title }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.videos.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="table-container" style="max-width: 720px;">
    <div class="table-inner p-3 p-md-4">
        <form action="{{ route('admin.videos.update', $post->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Video <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Link TikTok <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-tiktok"></i></span>
                    <input type="url" name="video_url" value="{{ old('video_url', $post->video_url) }}" class="form-control @error('video_url') is-invalid @enderror" required>
                    @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <small class="text-muted"><i class="bi bi-info-circle"></i> Ganti link untuk mengganti video yang diputar.</small>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">— Tanpa Kategori —</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $post->categories->first()->id ?? null) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Publish</option>
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.videos.index') }}" class="btn-admin btn-admin-secondary">Batal</a>
                <button type="submit" class="btn-admin btn-admin-primary"><i class="bi bi-save"></i> Perbarui Video</button>
            </div>
        </form>
    </div>
</div>
@endsection
