@extends('admin.layouts.app')
@section('title', 'Kategori')
@section('content')
<div class="page-header">
    <div>
        <h1>Kategori</h1>
        <p class="page-subtitle">Kelola kategori berita</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.categories.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kategori
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-tags"></i> Semua Kategori</h5>
        <span>{{ $categories->total() }} kategori</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Deskripsi</th>
                    <th>Berita</th>
                    <th style="width:15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                <tr>
                    <td>{{ $categories->firstItem() + $index }}</td>
                    <td>{{ $category->name }}</td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>
                    <td><span class="badge-admin badge-admin-orange">{{ $category->posts_count ?? $category->posts->count() ?? 0 }}</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
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
                    <td colspan="6">Belum ada kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $categories->firstItem() }}-{{ $categories->lastItem() }} dari {{ $categories->total() }}</span>
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
