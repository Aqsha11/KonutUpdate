@extends('admin.layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="page-header">
    <div>
        <h1>Profil Saya</h1>
        <p class="page-subtitle">Kelola informasi akun Anda</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card-admin">
            <div class="card-admin-body profile-card">
                <div class="avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
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
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
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
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror">
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control">
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
