@extends('admin.layouts.app')
@section('title', 'Tambah Role')
@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Role</h1>
        <p class="page-subtitle">Buat role baru dengan hak akses</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.roles.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label">Nama Role <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="slug" class="form-label">Slug <span class="required">*</span></label>
                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required placeholder="super_admin">
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">URL unik untuk role. Contoh: <code>super_admin</code>, <code>editor</code></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Permissions</label>
            <div class="row">
                @forelse($permissions as $permission)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" name="permissions[]" id="perm_{{ $permission->id }}" value="{{ $permission->id }}" class="form-check-input" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                        <label for="perm_{{ $permission->id }}" class="form-check-label">{{ $permission->name }}</label>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-secondary">Belum ada permission. <a href="{{ route('admin.permissions.create') }}">Buat permission</a> terlebih dahulu.</p>
                </div>
                @endforelse
            </div>
        </div>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-save"></i> Simpan
        </button>
    </form>
</div>
@endsection
