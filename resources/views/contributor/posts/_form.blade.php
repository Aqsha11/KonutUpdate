@if(isset($post) && $post->rejection_reason)
<div class="alert-admin alert-admin-danger mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>Kiriman ini ditolak.</strong> Perbaiki sesuai catatan admin lalu kirim ulang.
    <div class="mt-2"><strong>Alasan:</strong> {{ $post->rejection_reason }}</div>
</div>
@endif

<div class="form-card">
    <form action="{{ isset($post) ? route('kontributor.posts.update', $post->id) : route('kontributor.posts.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($post)) @method('PUT') @endif

        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">Jenis Konten <span class="required">*</span></label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input type="radio" name="type" id="typeArticle" value="article" class="form-check-input"
                                {{ old('type', isset($post) ? $post->type : 'article') === 'article' ? 'checked' : '' }}>
                            <label for="typeArticle" class="form-check-label">Berita</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="type" id="typeOpini" value="opini" class="form-check-input"
                                {{ old('type', isset($post) ? $post->type : '') === 'opini' ? 'checked' : '' }}>
                            <label for="typeOpini" class="form-check-label">Opini</label>
                        </div>
                    </div>
                    @error('type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="title" class="form-label">Judul <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                           placeholder="Judul kiriman" value="{{ old('title', $post->title ?? '') }}" required maxlength="255">
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="excerpt" class="form-label">Ringkasan</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror"
                              placeholder="Ringkasan singkat (opsional)" maxlength="500">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                    @error('excerpt')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="editor" class="form-label">Isi <span class="required">*</span></label>
                    <textarea name="body" id="editor" rows="12" class="form-control @error('body') is-invalid @enderror"
                              style="display:none;">{{ old('body', $post->body ?? '') }}</textarea>
                    <div id="editor-container" style="min-height:400px;"></div>
                    @error('body')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Kategori <span class="required">*</span> <small class="text-muted">(Maks 3)</small></label>
                    <div class="border rounded p-3 @error('category_ids') border-danger @enderror" style="max-height:200px;overflow-y:auto;">
                        @foreach($categories as $category)
                        <div class="form-check mb-1">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="cat_{{ $category->id }}"
                                   class="form-check-input category-checkbox"
                                   {{ in_array($category->id, old('category_ids', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                            <label for="cat_{{ $category->id }}" class="form-check-label">{{ $category->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    @error('category_ids')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kecamatan_id" class="form-label">Kecamatan</label>
                    <select name="kecamatan_id" id="kecamatan_id" class="form-select @error('kecamatan_id') is-invalid @enderror">
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $post->kecamatan_id ?? '') == $kecamatan->id ? 'selected' : '' }}>
                            {{ $kecamatan->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('kecamatan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tags" class="form-label">Tags</label>
                    <input type="text" name="tags" id="tags" class="form-control"
                           value="{{ old('tags', isset($post) ? $post->tags->pluck('name')->implode(', ') : '') }}"
                           placeholder="pisahkan dengan koma">
                    <div class="form-text">Contoh: pembangunan, ekonomi, pendidikan</div>
                </div>

                <div class="form-group">
                    <label for="thumbnail" class="form-label">Thumbnail <small class="text-muted">(opsional, 16:9)</small></label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                    @error('thumbnail')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="thumbnailPreview" class="mt-2"></div>
                    @if(isset($post) && $post->thumbnail)
                    <div class="mt-2">
                        <img src="{{ Storage::url($post->thumbnail) }}" alt="Thumbnail saat ini" class="img-preview">
                        <div class="form-text">Thumbnail saat ini.</div>
                    </div>
                    @endif
                </div>

                <div class="contributor-notice">
                    <i class="bi bi-info-circle"></i>
                    <span>Setelah dikirim untuk verifikasi, admin akan meninjau kiriman Anda. Konten yang tidak sesuai
                        ketentuan dapat ditolak beserta alasannya.</span>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="action" value="submit" class="btn-admin btn-admin-primary">
                        <i class="bi bi-send"></i> {{ isset($post) ? 'Kirim Ulang untuk Verifikasi' : 'Kirim untuk Verifikasi' }}
                    </button>
                    <button type="submit" name="action" value="draft" class="btn-admin btn-admin-secondary">
                        <i class="bi bi-save"></i> Simpan Draft
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .ck-editor__editable { min-height: 400px; }
    .ck.ck-editor { border-radius: 10px; overflow: hidden; border: 1.5px solid var(--border); }
    .ck.ck-toolbar { border: none !important; border-bottom: 1px solid var(--border) !important; background: #F8FAFC !important; }
    .ck.ck-content { border: none !important; }
    .contributor-notice { display: flex; gap: 8px; align-items: flex-start; background: var(--primary-light); border: 1px solid var(--primary-container); color: var(--text-secondary); border-radius: 10px; padding: 12px; font-size: 0.82rem; line-height: 1.5; margin-bottom: 16px; }
    .contributor-notice i { color: var(--primary); margin-top: 2px; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initCKEditor('editor', 'Tulis konten di sini...', '{{ route('kontributor.posts.upload-image') }}');

        var thumbnail = document.getElementById('thumbnail');
        if (thumbnail) {
            thumbnail.addEventListener('change', function(e) {
                var preview = document.getElementById('thumbnailPreview');
                preview.innerHTML = '';
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(ev) {
                        var img = document.createElement('img');
                        img.src = ev.target.result;
                        img.className = 'img-preview';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

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
