@extends('admin.layouts.app')
@section('title', 'Role')
@section('content')
<div class="page-header">
    <div>
        <h1>Role</h1>
        <p class="page-subtitle">Kelola role dan hak akses</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.roles.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Role
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-shield-lock"></i> Semua Role</h5>
        <span>{{ $roles->total() }} role</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Nama Role</th>
                    <th>Slug</th>
                    <th>Deskripsi</th>
                    <th>User</th>
                    <th style="width:15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $index => $role)
                <tr>
                    <td>{{ $roles->firstItem() + $index }}</td>
                    <td>{{ $role->name }}</td>
                    <td><code>{{ $role->slug }}</code></td>
                    <td>{{ Str::limit($role->description, 50) }}</td>
                    <td><span class="badge-admin badge-admin-info">{{ $role->users_count ?? $role->users()->count() }}</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="6">Belum ada role.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($roles->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $roles->firstItem() }}-{{ $roles->lastItem() }} dari {{ $roles->total() }}</span>
        {{ $roles->links() }}
    </div>
    @endif
</div>
@endsection
