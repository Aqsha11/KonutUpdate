@extends('frontend.layouts.app')

@section('title', 'Tulis Opini - ' . ($site_settings['site_name'] ?? 'Konut.Update'))

@section('meta')
    <meta name="description" content="Kirim opini Anda ke {{ $site_settings['site_name'] ?? 'Konut.Update' }}">
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:title" content="Tulis Opini - {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:description" content="Kirim opini Anda ke {{ $site_settings['site_name'] ?? 'Konut.Update' }}" />
    <meta property="og:type" content="website" />
@endsection

@section('content')
    <div class="mb-3">
        <nav class="breadcrumb">
            <a href="{{ url('/') }}">Beranda</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <a href="{{ route('opini') }}">Opini</a>
            <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
            <span>Tulis Opini</span>
        </nav>
        <h1 class="page-title">
            <span class="page-title-icon bg-primary-light text-primary"><i data-lucide="pencil-line" class="w-4 h-4"></i></span>
            Tulis Opini
        </h1>
        <p class="text-on-surface-variant text-xs mt-1">Bagikan pandangan Anda tanpa perlu login. Opini akan ditinjau admin terlebih dahulu sebelum tayang.</p>
    </div>

    <form action="{{ route('opini.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Honeypot anti-spam --}}
        <div class="hidden" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>
        <input type="hidden" name="trap_time" id="trap_time" value="0">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2 space-y-5">
                <div class="bg-surface border border-outline rounded-xl p-4 md:p-6">
                    <div class="mb-4">
                        <label for="title" class="block text-xs font-semibold text-on-surface mb-1.5">Judul Opini <span class="text-accent">*</span></label>
                        <input type="text" name="title" id="title" required maxlength="191" value="{{ old('title') }}" placeholder="Judul opini Anda"
                            class="ku-form-input @error('title') border-red-400 @enderror">
                        @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="excerpt" class="block text-xs font-semibold text-on-surface mb-1.5">Ringkasan <span class="text-on-surface-variant font-normal">(opsional)</span></label>
                        <textarea name="excerpt" id="excerpt" rows="3" maxlength="500" placeholder="Ringkasan opini (opsional)"
                            class="ku-form-input @error('excerpt') border-red-400 @enderror">{{ old('excerpt') }}</textarea>
                        @error('excerpt')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-on-surface mb-1.5">Isi Opini <span class="text-accent">*</span></label>
                        <textarea name="body" id="editor" rows="12" style="display:none;">{{ old('body') }}</textarea>
                        @error('body')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex gap-2">
                            <button type="button" onclick="togglePreview()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline bg-surface text-on-surface-variant text-xs font-semibold hover:bg-surface-container transition-colors">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Preview
                            </button>
                        </div>
                        <div id="preview" class="ku-preview hidden mt-3 p-4 border border-outline rounded-lg bg-surface"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <div class="bg-surface border border-outline rounded-xl p-4">
                    <h5 class="text-sm font-bold text-on-surface mb-3 flex items-center gap-1.5">
                        <i data-lucide="user-round" class="w-4 h-4 text-primary"></i> Data Penulis
                    </h5>
                    <div class="space-y-3">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-on-surface mb-1.5">Nama <span class="text-accent">*</span></label>
                            <input type="text" name="name" id="name" required maxlength="100" value="{{ old('name') }}" placeholder="Nama Anda"
                                class="ku-form-input @error('name') border-red-400 @enderror">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-semibold text-on-surface mb-1.5">Email <span class="text-accent">*</span></label>
                            <input type="email" name="email" id="email" required maxlength="191" value="{{ old('email') }}" placeholder="nama@gmail.com"
                                class="ku-form-input @error('email') border-red-400 @enderror">
                            @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-surface border border-outline rounded-xl p-4">
                    <label class="block text-xs font-semibold text-on-surface mb-2">Kategori <small class="text-on-surface-variant font-normal">(maks 3)</small></label>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1 @error('category_ids') border border-red-400 rounded-lg p-2 @enderror">
                        @foreach($categories as $category)
                        <label class="flex items-start gap-2 py-0.5 cursor-pointer">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="cat_{{ $category->id }}"
                                class="ku-checkbox category-checkbox mt-0.5" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                            <span class="text-[13px] text-on-surface leading-snug">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <input type="hidden" name="category_id" value="{{ old('category_id') }}">
                    @error('category_ids')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-surface border border-outline rounded-xl p-4">
                    <label for="kecamatan_id" class="block text-xs font-semibold text-on-surface mb-1.5">Kecamatan <span class="text-on-surface-variant font-normal">(opsional)</span></label>
                    <select name="kecamatan_id" id="kecamatan_id"
                        class="ku-form-input @error('kecamatan_id') border-red-400 @enderror">
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->name }}</option>
                        @endforeach
                    </select>
                    @error('kecamatan_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-surface border border-outline rounded-xl p-4">
                    <label for="tags" class="block text-xs font-semibold text-on-surface mb-1.5">Tags</label>
                    <input type="text" name="tags" id="tags" maxlength="255" value="{{ old('tags') }}" placeholder="pisahkan dengan koma"
                        class="ku-form-input">
                    <p class="text-on-surface-variant text-[11px] mt-1">Contoh: politik, ekonomi, olahraga</p>
                </div>

                <div class="bg-surface border border-outline rounded-xl p-4">
                    <label for="thumbnail" class="block text-xs font-semibold text-on-surface mb-1.5">Thumbnail <span class="text-on-surface-variant font-normal">(opsional, landscape 16:9, 1–5MB)</span></label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                        class="ku-form-input @error('thumbnail') border-red-400 @enderror">
                    @error('thumbnail')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div id="thumbnailPreview" class="mt-2"></div>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-primary text-white text-sm font-semibold no-underline hover:opacity-90 transition-opacity">
                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Opini
                </button>
                <p class="text-on-surface-variant text-[11px] flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 shrink-0"></i> Kiriman Anda akan diverifikasi admin terlebih dahulu sebelum tayang.
                </p>
            </div>
        </div>
    </form>
@endsection

@push('styles')
<style>
    .ku-form-input {
        width: 100%;
        padding: 0.6rem 0.85rem;
        border-radius: 0.5rem;
        border: 1px solid var(--color-outline);
        background: var(--color-surface);
        color: var(--color-on-surface);
        font-size: 0.85rem;
        line-height: 1.5;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .ku-form-input:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(24, 155, 57, 0.15);
    }
    .ku-form-input::placeholder { color: var(--color-on-surface-variant); opacity: 0.7; }
    .ku-checkbox { accent-color: var(--color-primary); width: 15px; height: 15px; }
    .ku-preview:not(.hidden) { display: block; }
    .ku-preview .ck-content * { max-width: 100%; }

    .ck-editor__editable { min-height: 400px; }
    .ck-editor__editable a { color: var(--color-accent); }
    .ck.ck-editor { border-radius: 10px; overflow: hidden; border: 1.5px solid var(--color-outline); transition: border-color 0.3s; }
    .ck.ck-editor:focus-within { border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(24,155,57,0.08); }
    .ck.ck-toolbar { border: none !important; border-bottom: 1px solid var(--color-outline) !important; background: var(--color-surface-container) !important; }
    .ck.ck-content { border: none !important; }
</style>
@endpush

@push('scripts')
@vite('resources/js/opini-editor.js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('trap_time').value = Math.floor(Date.now() / 1000);

        initOpiniEditor('editor', 'Tulis opini Anda di sini...', '{{ route('opini.upload-image') }}');

        document.getElementById('thumbnail').addEventListener('change', function(e) {
            var preview = document.getElementById('thumbnailPreview');
            preview.innerHTML = '';
            var file = e.target.files[0];
            if (!file) return;
            if (file.size < 1 * 1024 * 1024) {
                preview.innerHTML = '<p class="text-red-500 text-[11px] mt-1">Ukuran gambar minimal 1MB. Pilih gambar lain.</p>';
                this.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                preview.innerHTML = '<p class="text-red-500 text-[11px] mt-1">Ukuran gambar maksimal 5MB. Pilih gambar lain.</p>';
                this.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function(ev) {
                var img = new Image();
                img.onload = function() {
                    var ratio = img.naturalWidth / img.naturalHeight;
                    if (img.naturalWidth <= img.naturalHeight || ratio < 1.6 || ratio > 2.0) {
                        preview.innerHTML = '<p class="text-red-500 text-[11px] mt-1">Gambar harus berorientasi landscape 16:9. Pilih gambar lain.</p>';
                        e.target.value = '';
                        return;
                    }
                    img.className = 'mt-1 rounded-lg border border-outline max-w-full';
                    img.style.aspectRatio = '16 / 9';
                    img.style.objectFit = 'cover';
                    preview.appendChild(img);
                };
                img.onerror = function() {
                    preview.innerHTML = '<p class="text-red-500 text-[11px] mt-1">File tidak dapat dibaca sebagai gambar.</p>';
                    e.target.value = '';
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
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

    window.togglePreview = function() {
        var preview = document.getElementById('preview');
        var data = window.editorInstance ? window.editorInstance.getData() : document.getElementById('editor')?.value || '';
        preview.innerHTML = data || '<p class="text-on-surface-variant text-sm">Konten belum ditulis.</p>';
        preview.classList.toggle('hidden');
    };
</script>
@endpush
