@extends('admin.layouts.app')
@section('title', 'Pengaturan')
@section('content')
<div class="page-header">
    <div>
        <h1>Pengaturan Website</h1>
        <p class="page-subtitle">Konfigurasi portal berita Anda</p>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-card">
        <div class="settings-section">
            <div class="section-title"><i class="bi bi-info-circle"></i> Informasi Umum</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="site_name" class="form-label">Nama Situs</label>
                        <input type="text" name="site_name" id="site_name" class="form-control @error('site_name') is-invalid @enderror" placeholder="Nama Website" value="{{ old('site_name', $settings->site_name ?? '') }}">
                        @error('site_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tagline" class="form-label">Tagline</label>
                        <input type="text" name="tagline" id="tagline" class="form-control @error('tagline') is-invalid @enderror" placeholder="Tagline website" value="{{ old('tagline', $settings->tagline ?? '') }}">
                        @error('tagline')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com" value="{{ old('email', $settings->email ?? '') }}">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="phone" class="form-label">Telepon</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Nomor telepon" value="{{ old('phone', $settings->phone ?? '') }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="address" class="form-label">Alamat</label>
                        <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Alamat lengkap" value="{{ old('address', $settings->address ?? '') }}">
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="settings-section">
            <div class="section-title"><i class="bi bi-share"></i> Media Sosial</div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="facebook" class="form-label">Facebook</label>
                        <div class="input-group-custom">
                            <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                            <input type="url" name="facebook" id="facebook" class="form-control @error('facebook') is-invalid @enderror" value="{{ old('facebook', $settings->facebook ?? '') }}" placeholder="https://facebook.com/...">
                        </div>
                        @error('facebook')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="instagram" class="form-label">Instagram</label>
                        <div class="input-group-custom">
                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                            <input type="url" name="instagram" id="instagram" class="form-control @error('instagram') is-invalid @enderror" value="{{ old('instagram', $settings->instagram ?? '') }}" placeholder="https://instagram.com/...">
                        </div>
                        @error('instagram')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tiktok" class="form-label">TikTok</label>
                        <div class="input-group-custom">
                            <span class="input-group-text"><i class="fab fa-tiktok"></i></span>
                            <input type="url" name="tiktok" id="tiktok" class="form-control @error('tiktok') is-invalid @enderror" value="{{ old('tiktok', $settings->tiktok ?? '') }}" placeholder="https://tiktok.com/...">
                        </div>
                        @error('tiktok')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="whatsapp" class="form-label">WhatsApp</label>
                        <div class="input-group-custom">
                            <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp', $settings->whatsapp ?? '') }}" placeholder="https://wa.me/62...">
                        </div>
                        @error('whatsapp')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="youtube" class="form-label">YouTube</label>
                        <div class="input-group-custom">
                            <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                            <input type="url" name="youtube" id="youtube" class="form-control @error('youtube') is-invalid @enderror" value="{{ old('youtube', $settings->youtube ?? '') }}" placeholder="https://youtube.com/...">
                        </div>
                        @error('youtube')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="settings-section">
            <div class="section-title"><i class="bi bi-image"></i> Logo & Favicon</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="logo" class="form-label">Logo</label>
                        @if(isset($settings->logo) && $settings->logo)
                        <div class="mb-2">
                            <img src="{{ Storage::url($settings->logo) }}" alt="Logo" class="img-preview">
                        </div>
                        @endif
                        <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                        @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="logoPreview" class="mt-2"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="favicon" class="form-label">Favicon</label>
                        @if(isset($settings->favicon) && $settings->favicon)
                        <div class="mb-2">
                            <img src="{{ Storage::url($settings->favicon) }}" alt="Favicon" class="favicon-preview">
                        </div>
                        @endif
                        <input type="file" name="favicon" id="favicon" class="form-control @error('favicon') is-invalid @enderror" accept="image/*">
                        @error('favicon')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="faviconPreview" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="settings-section">
            <div class="section-title"><i class="bi bi-palette"></i> Tema & Warna</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="primary_color" class="form-label">Warna Utama (Primary)</label>
                        <div class="input-group-custom">
                            <input type="color" name="primary_color" id="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $settings->primary_color ?? '#189B39') }}" style="max-width:60px;padding:2px;cursor:pointer;">
                            <input type="text" class="form-control" id="primary_color_hex" value="{{ old('primary_color', $settings->primary_color ?? '#189B39') }}" maxlength="7" style="flex:1;font-family:monospace;">
                        </div>
                        <small class="text-muted">Warna default: #189B39 (hijau)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="accent_color" class="form-label">Warna Aksen (Accent)</label>
                        <div class="input-group-custom">
                            <input type="color" name="accent_color" id="accent_color" class="form-control form-control-color" value="{{ old('accent_color', $settings->accent_color ?? '#F58220') }}" style="max-width:60px;padding:2px;cursor:pointer;">
                            <input type="text" class="form-control" id="accent_color_hex" value="{{ old('accent_color', $settings->accent_color ?? '#F58220') }}" maxlength="7" style="flex:1;font-family:monospace;">
                        </div>
                        <small class="text-muted">Warna default: #F58220 (oranye)</small>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="settings-section">
            <div class="section-title"><i class="bi bi-search"></i> SEO</div>
            <div class="form-group">
                <label for="meta_title" class="form-label">Meta Title</label>
                <input type="text" name="meta_title" id="meta_title" class="form-control @error('meta_title') is-invalid @enderror" placeholder="Meta title untuk SEO" value="{{ old('meta_title', $settings->meta_title ?? '') }}">
                @error('meta_title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="3" class="form-control @error('meta_description') is-invalid @enderror" placeholder="Meta description untuk SEO">{{ old('meta_description', $settings->meta_description ?? '') }}</textarea>
                @error('meta_description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                <textarea name="meta_keywords" id="meta_keywords" rows="2" class="form-control @error('meta_keywords') is-invalid @enderror" placeholder="pisahkan dengan koma">{{ old('meta_keywords', $settings->meta_keywords ?? '') }}</textarea>
                @error('meta_keywords')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
            <button type="submit" class="btn-admin btn-admin-primary btn-admin-lg">
                <i class="bi bi-save"></i> Simpan Pengaturan
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('logo').addEventListener('change', function(e) {
        var preview = document.getElementById('logoPreview');
        preview.innerHTML = '';
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-preview';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
    document.getElementById('favicon').addEventListener('change', function(e) {
        var preview = document.getElementById('faviconPreview');
        preview.innerHTML = '';
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:32px;height:32px;border-radius:6px;border:1px solid var(--border);';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });

    // Sync color picker with hex input
    ['primary_color', 'accent_color'].forEach(function(id) {
        var picker = document.getElementById(id);
        var hex = document.getElementById(id + '_hex');
        if (picker && hex) {
            picker.addEventListener('input', function() { hex.value = this.value; });
            hex.addEventListener('input', function() {
                if (/^#[0-9a-f]{6}$/i.test(this.value)) { picker.value = this.value; }
            });
        }
    });
</script>
@endpush
