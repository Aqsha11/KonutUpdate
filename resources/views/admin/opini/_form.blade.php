<div class="form-card">
    <form action="{{ isset($post) ? route('admin.opini.update', $post->id) : route('admin.opini.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($post)) @method('PUT') @endif

        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="title" class="form-label">Judul Opini <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Judul opini" value="{{ old('title', $post->title ?? '') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="excerpt" class="form-label">Ringkasan</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror" placeholder="Ringkasan opini (opsional)">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                    @error('excerpt')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="editor" class="form-label">Isi Opini <span class="required">*</span></label>
                    <textarea name="body" id="editor" rows="12" class="form-control @error('body') is-invalid @enderror" placeholder="Tulis isi opini di sini..." style="display:none;">{{ old('body', $post->body ?? '') }}</textarea>
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
                    <label class="form-label">Kategori <span class="required">*</span> <small class="text-muted">(Maks 3)</small></label>
                    <div class="border rounded p-3 @error('category_ids') border-danger @enderror" style="max-height:200px;overflow-y:auto;">
                        @foreach($categories as $category)
                        <div class="form-check mb-1">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="cat_{{ $category->id }}" class="form-check-input category-checkbox" {{ in_array($category->id, old('category_ids', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                            <label for="cat_{{ $category->id }}" class="form-check-label">{{ $category->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="category_id" value="{{ old('category_id', $post->category_id ?? '') }}">
                    @error('category_ids')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="kecamatan_id" class="form-label">Kecamatan</label>
                    <select name="kecamatan_id" id="kecamatan_id" class="form-select @error('kecamatan_id') is-invalid @enderror">
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $post->kecamatan_id ?? '') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->name }}{{ $kecamatan->description ? ' (' . $kecamatan->description . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('kecamatan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="tags" class="form-label">Tags</label>
                    <input type="text" name="tags" id="tags" class="form-control" value="{{ old('tags', isset($post) ? $post->tags->pluck('name')->implode(', ') : '') }}" placeholder="pisahkan dengan koma">
                    <div class="form-text">Contoh: politik, ekonomi, olahraga</div>
                </div>
                <div class="form-group">
                    <label for="thumbnail" class="form-label">Thumbnail <span class="text-muted" style="font-size:0.75rem;">(opsional, 16:9)</span></label>
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
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check">
                            <input type="radio" name="status" id="statusDraft" value="draft" class="form-check-input" {{ old('status', isset($post) ? $post->status : 'draft') === 'draft' ? 'checked' : '' }}>
                            <label for="statusDraft" class="form-check-label">Draft</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="status" id="statusPublished" value="published" class="form-check-input" {{ old('status', isset($post) ? $post->status : '') === 'published' ? 'checked' : '' }}>
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
                            <input type="checkbox" name="is_breaking" id="isBreaking" value="1" class="form-check-input" {{ old('is_breaking', isset($post) && $post->is_breaking) ? 'checked' : '' }}>
                            <label for="isBreaking" class="form-check-label">Breaking News</label>
                        </div>
                        <div class="form-check">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" id="isFeatured" value="1" class="form-check-input" {{ old('is_featured', isset($post) && $post->is_featured) ? 'checked' : '' }}>
                            <label for="isFeatured" class="form-check-label">Konten Pilihan</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 fw-semibold">Headline</label>
                            <label class="toggle-switch mb-0">
                                <input type="hidden" name="is_headline" value="0">
                                <input type="checkbox" name="is_headline" id="isHeadline" value="1" {{ old('is_headline', isset($post) && $post->is_headline) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">Headline &amp; Breaking News akan berakhir otomatis setelah 7 hari.</small>
                </div>
                <div class="form-group">
                    <label for="published_at" class="form-label">Published At</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="form-control" placeholder="Tanggal Publikasi" value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                </div>
                <button type="submit" class="btn-admin btn-admin-primary btn-admin-block">
                    <i class="bi bi-save"></i> {{ isset($post) ? 'Perbarui Opini' : 'Simpan Opini' }}
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .ck-editor__editable { min-height: 500px; }
    .ck-editor__editable a { color: var(--accent, #FF6B00); }
    .ck.ck-editor { border-radius: 10px; overflow: hidden; border: 1.5px solid var(--border); transition: border-color 300ms; }
    .ck.ck-editor:focus-within { border-color: var(--primary, #189B39); box-shadow: 0 0 0 3px rgba(24,155,57,0.08); }
    .ck.ck-toolbar { border: none !important; border-bottom: 1px solid var(--border) !important; background: #F8FAFC !important; }
    .ck.ck-content { border: none !important; }

    .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; cursor: pointer; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; inset: 0; background: #cbd5e1; border-radius: 24px; transition: all 0.3s; }
    .toggle-slider::before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: all 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
    .toggle-switch input:checked + .toggle-slider { background: #189B39; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initCKEditor('editor', 'Tulis isi opini di sini...', '{{ route('admin.opini.upload-image') }}');

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
