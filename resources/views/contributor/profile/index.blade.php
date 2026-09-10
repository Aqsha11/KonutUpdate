@extends('contributor.layouts.app')
@section('title', 'Profil Akun')
@section('content')
<div class="page-header">
    <div>
        <h1>Profil Akun</h1>
        <p class="page-subtitle">Kelola informasi akun dan kata sandi Anda</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card-admin">
            <div class="card-admin-body profile-card">
                @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-lg" style="object-fit:cover;">
                @else
                <div class="avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                <span class="badge-admin badge-admin-orange">{{ $user->role ? ucfirst(str_replace('_', ' ', $user->role->name)) : 'Unknown' }}</span>
                <hr>
                <div class="info-row">
                    <i class="bi bi-calendar3"></i> Bergabung: {{ $user->created_at->format('d F Y') }}
                </div>
                <div class="info-row">
                    <i class="bi bi-pencil-square"></i> Total Postingan: {{ $user->posts()->count() }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-card">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="avatar" class="form-label">Foto Profil</label>
                    <input type="file" name="avatar" id="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/jpeg,image/png,image/gif,image/webp">
                    @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">JPG/PNG/GIF/WebP, maks 2MB. Di-crop persegi otomatis.</small>
                </div>
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <hr>
                <h6 class="fw-bold mb-3">Ganti Password <span class="text-secondary fw-normal">(opsional)</span></h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Password saat ini">
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Minimal 8 karakter, kombinasi huruf kapital & angka">
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Konfirmasi password baru">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
