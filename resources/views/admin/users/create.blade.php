@extends('admin.layouts.app')
@section('title', 'Tambah User')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah User</h1>
        <p class="page-subtitle">Buat akun pengguna baru</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label">Nama <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email" class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com" value="{{ old('email') }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password" class="form-label">Password <span class="required">*</span></label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Konfirmasi Password" required>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="role_id" class="form-label">Role <span class="required">*</span></label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-save"></i> Simpan
        </button>
    </form>
</div>
@endsection
