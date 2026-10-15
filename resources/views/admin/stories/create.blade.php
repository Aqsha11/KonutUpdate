@extends('admin.layouts.app')
@section('title', 'Tambah Story')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Story</h1>
        <p class="page-subtitle">Story berakhir otomatis {{ \App\Models\Story::EXPIRE_DAYS }} hari setelah dibuat</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.stories.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="table-container" style="max-width: 720px;">
    <div class="table-inner p-3 p-md-4">
        <form action="{{ route('admin.stories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted"><i class="bi bi-info-circle"></i> Judul story otomatis mengikuti nama kategori.</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Gambar Story <span class="text-danger">*</span></label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control @error('image') is-invalid @enderror" required>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted"><i class="bi bi-info-circle"></i> Disarankan gambar potret (memanjang ke bawah). Maksimal 4MB.</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Link Tujuan (opsional)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input type="url" name="url" value="{{ old('url') }}" class="form-control @error('url') is-invalid @enderror" placeholder="https://...">
                    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <small class="text-muted"><i class="bi bi-info-circle"></i> Jika diisi, tombol "Baca Selengkapnya" muncul di dalam story.</small>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
                    <small class="text-muted">Angka kecil tampil lebih dulu.</small>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktifkan sekarang</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.stories.index') }}" class="btn-admin btn-admin-secondary">Batal</a>
                <button type="submit" class="btn-admin btn-admin-primary"><i class="bi bi-save"></i> Simpan Story</button>
            </div>
        </form>
    </div>
</div>
@endsection
