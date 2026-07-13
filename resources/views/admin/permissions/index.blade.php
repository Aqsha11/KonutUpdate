@extends('admin.layouts.app')
@section('title', 'Permission')
@section('content')
<div class="page-header">
    <div>
        <h1>Permission</h1>
        <p class="page-subtitle">Kelola hak akses sistem</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.permissions.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Permission
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-key"></i> Semua Permission</h5>
        <span>{{ $permissions->total() }} permission</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Nama Permission</th>
                    <th>Slug</th>
                    <th>Deskripsi</th>
                    <th style="width:15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $index => $permission)
                <tr>
                    <td>{{ $permissions->firstItem() + $index }}</td>
                    <td>{{ $permission->name }}</td>
                    <td><code>{{ $permission->slug }}</code></td>
                    <td>{{ Str::limit($permission->description, 60) }}</td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST">
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
                    <td colspan="5">Belum ada permission.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($permissions->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $permissions->firstItem() }}-{{ $permissions->lastItem() }} dari {{ $permissions->total() }}</span>
        {{ $permissions->links() }}
    </div>
    @endif
</div>
@endsection
