@extends('admin.layouts.app')
@section('title', 'Tambah Berita')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Berita</h1>
        <p class="page-subtitle">Buat berita baru</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.posts.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="title" class="form-label">Judul <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Judul Berita" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="excerpt" class="form-label">Ringkasan</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror" placeholder="Ringkasan berita (opsional)">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="editor" class="form-label">Isi Berita <span class="required">*</span></label>
                    <textarea name="body" id="editor" rows="12" class="form-control @error('body') is-invalid @enderror" placeholder="Tulis konten berita di sini..." style="display:none;">{{ old('body') }}</textarea>
                    <div id="editor-container" style="min-height:500px;"></div>
                    @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="mt-2 d-flex gap-2">
                        <button type="button" class="btn-admin btn-admin-sm btn-admin-secondary" onclick="togglePreview()">
                            <i class="bi bi-eye"></i> Preview
                        </button>
                    </div>
                    <div id="preview" class="mt-3 p-4 border rounded d-none"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="type" class="form-label">Jenis Berita <span class="required">*</span></label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                        <option value="article" {{ old('type', 'article') === 'article' ? 'selected' : '' }}>Artikel (Gambar)</option>
                        <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video</option>
                    </select>
                    @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group" id="videoFields" style="display: {{ old('type') === 'video' ? 'block' : 'none' }};">
                    <label class="form-label">Sumber Video</label>
                    <ul class="nav nav-tabs nav-tabs-sm mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-upload">Upload File</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-url">URL Eksternal</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-upload">
                            <input type="file" name="video_file" id="video_file" class="form-control @error('video_file') is-invalid @enderror" accept="video/mp4,video/webm,video/quicktime">
                            <div class="form-text">Format: MP4, WebM, MOV. Maks 50MB.</div>
                            @error('video_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="videoPreview" class="mt-2"></div>
                        </div>
                        <div class="tab-pane fade" id="tab-url">
                            <input type="url" name="video_url" id="video_url" class="form-control @error('video_url') is-invalid @enderror" value="{{ old('video_url') }}" placeholder="https://youtube.com/watch?v=...">
                            <div class="form-text">YouTube, Vimeo, atau URL video lainnya.</div>
                            @error('video_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori <span class="required">*</span> <small class="text-muted">(Maks 3)</small></label>
                    <div class="border rounded p-3 @error('category_ids') border-danger @enderror" style="max-height:200px;overflow-y:auto;">
                        @foreach($categories as $category)
                        <div class="form-check mb-1">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="cat_{{ $category->id }}" class="form-check-input category-checkbox" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                            <label for="cat_{{ $category->id }}" class="form-check-label">{{ $category->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="category_id" value="{{ old('category_id') }}">
                    @error('category_ids')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="kecamatan_id" class="form-label">Kecamatan</label>
                    <select name="kecamatan_id" id="kecamatan_id" class="form-select @error('kecamatan_id') is-invalid @enderror">
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->name }}{{ $kecamatan->description ? ' (' . $kecamatan->description . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('kecamatan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="tags" class="form-label">Tags</label>
                    <input type="text" name="tags" id="tags" class="form-control" value="{{ old('tags') }}" placeholder="pisahkan dengan koma">
                    <div class="form-text">Contoh: politik, ekonomi, olahraga</div>
                </div>
                <div class="form-group">
                    <!-- Label thumbnail: teks berubah dinamis via JS (16:9 / bebas) sesuai toggle Headline -->
                    <label for="thumbnail" class="form-label">Thumbnail <span class="text-muted" style="font-size:0.75rem;">(crop otomatis <span id="cropLabel">16:9</span>)</span></label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                    @error('thumbnail')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="thumbnailPreview" class="mt-2"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input type="radio" name="status" id="statusDraft" value="draft" class="form-check-input" {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}>
                            <label for="statusDraft" class="form-check-label">Draft</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status" id="statusPublished" value="published" class="form-check-input" {{ old('status') === 'published' ? 'checked' : '' }}>
                            <label for="statusPublished" class="form-check-label">Published</label>
                        </div>
                    </div>
                    @error('status')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="d-flex gap-4 flex-wrap">
                        <div class="form-check">
                            <input type="hidden" name="is_breaking" value="0">
                            <input type="checkbox" name="is_breaking" id="isBreaking" value="1" class="form-check-input" {{ old('is_breaking') ? 'checked' : '' }}>
                            <label for="isBreaking" class="form-check-label">Breaking News</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 fw-semibold">Headline</label>
                            <label class="toggle-switch mb-0">
                                <input type="hidden" name="is_headline" value="0">
                                <input type="checkbox" name="is_headline" id="isHeadline" value="1" {{ old('is_headline') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="published_at" class="form-label">Published At</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="form-control" placeholder="Tanggal Publikasi" value="{{ old('published_at') }}">
                </div>
                <button type="submit" class="btn-admin btn-admin-primary btn-admin-block">
                    <i class="bi bi-save"></i> Simpan Berita
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- Crop Modal --}}
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Thumbnail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 mb-3" style="font-size:0.85rem;">
                    <i class="bi bi-crop"></i> Atur posisi gambar agar sesuai dengan ukuran card (16:9).
                </div>
                <div class="crop-container text-center">
                    <img id="cropImage" style="max-width:100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-admin btn-admin-primary" id="cropConfirmBtn">
                    <i class="bi bi-check-lg"></i> Crop & Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .ck-editor__editable { min-height: 500px; }
    .ck-editor__editable a { color: #FF6B00; }
    .ck.ck-editor { border-radius: 10px; overflow: hidden; border: 1.5px solid var(--border); transition: border-color 300ms; }
    .ck.ck-editor:focus-within { border-color: #FF6B00; box-shadow: 0 0 0 3px rgba(255,107,0,0.08); }
    .ck.ck-toolbar { border: none !important; border-bottom: 1px solid var(--border) !important; background: #F8FAFC !important; }
    .ck.ck-content { border: none !important; }
    .nav-tabs-sm .nav-link { font-size: 0.8rem; padding: 0.35rem 0.75rem; }

    .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; cursor: pointer; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; inset: 0; background: #cbd5e1; border-radius: 24px; transition: all 0.3s; }
    .toggle-slider::before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: all 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
    .toggle-switch input:checked + .toggle-slider { background: #189B39; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }
</style>
@endpush

@push('scripts')
@vite('resources/js/admin.js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initCKEditor('editor');
        initThumbnailCropper('thumbnail', 'thumbnailPreview');

        var typeSelect = document.getElementById('type');
        var videoFields = document.getElementById('videoFields');
        typeSelect.addEventListener('change', function() {
            videoFields.style.display = this.value === 'video' ? 'block' : 'none';
        });

        // Update label crop saat toggle Headline berubah
        document.getElementById('isHeadline')?.addEventListener('change', function() {
            document.getElementById('cropLabel').textContent = this.checked ? 'bebas' : '16:9';
        });

        document.getElementById('video_file').addEventListener('change', function(e) {
            var preview = document.getElementById('videoPreview');
            preview.innerHTML = '';
            var file = e.target.files[0];
            if (file) {
                var size = (file.size / (1024 * 1024)).toFixed(1);
                var info = document.createElement('div');
                info.className = 'alert alert-info py-2 px-3 mb-0 mt-2';
                info.innerHTML = '<i class="bi bi-film"></i> ' + file.name + ' <span class="text-muted">(' + size + ' MB)</span>';
                preview.appendChild(info);
            }
        });

        document.querySelectorAll('.category-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var checked = document.querySelectorAll('.category-checkbox:checked');
                if (checked.length > 3) {
                    this.checked = false;
                    alert('Maksimal 3 kategori saja.');
                }
            });
        });
    });
</script>
@endpush
