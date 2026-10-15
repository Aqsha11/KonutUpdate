@extends('admin.layouts.app')
@section('title', 'Edit Story')
@section('content')
<div class="page-header">
    <div>
        <h1>Edit Story</h1>
        <p class="page-subtitle">Perbarui konten story yang sedang tayang</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.stories.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="table-container" style="max-width: 720px;">
    <div class="table-inner p-3 p-md-4">
        <form action="{{ route('admin.stories.update', $story->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) old('category_id', $story->category_id) === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted"><i class="bi bi-info-circle"></i> Judul story otomatis mengikuti nama kategori.</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Gambar Story</label>
                <div class="d-flex align-items-start gap-3">
                    <img src="{{ $story->image_url }}" alt="{{ $story->display_title }}" style="width:72px;height:96px;object-fit:cover;border-radius:8px;border:1px solid var(--border, #e5e7eb);">
                    <div class="flex-grow-1">
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Biarkan kosong bila tidak ingin mengganti gambar. Maksimal 4MB.</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Link Tujuan (opsional)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input type="url" name="url" value="{{ old('url', $story->url) }}" class="form-control @error('url') is-invalid @enderror" placeholder="https://...">
                    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $story->sort_order) }}" class="form-control" min="0">
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" {{ old('is_active', $story->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktifkan sekarang</label>
                    </div>
                </div>
            </div>

            <div class="mb-4 text-muted" style="font-size:0.82rem;">
                <i class="bi bi-clock-history"></i>
                @if($story->isExpired())
                    Story ini sudah berakhir. Gunakan tombol perpanjang di daftar story untuk mengaktifkan kembali.
                @else
                    Berakhir pada {{ $story->expires_at?->translatedFormat('d F Y H:i') }} ({{ $story->remaining_days }} hari lagi).
                @endif
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.stories.index') }}" class="btn-admin btn-admin-secondary">Batal</a>
                <button type="submit" class="btn-admin btn-admin-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
