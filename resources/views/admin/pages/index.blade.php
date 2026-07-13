@extends('admin.layouts.app')
@section('title', 'Halaman')
@section('content')
<div class="page-header">
    <div>
        <h1>Halaman</h1>
        <p class="page-subtitle">Kelola halaman statis website</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.pages.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Halaman
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-file-text"></i> Semua Halaman</h5>
        <span>{{ $pages->total() }} halaman</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Judul</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th style="width:15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $index => $page)
                <tr>
                    <td>{{ $pages->firstItem() + $index }}</td>
                    <td>{{ $page->title }}</td>
                    <td><code>{{ $page->slug }}</code></td>
                    <td>
                        @if($page->is_published)
                            <span class="badge-admin badge-admin-success">Published</span>
                        @else
                            <span class="badge-admin badge-admin-secondary">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST">
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
                    <td colspan="5">Belum ada halaman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pages->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $pages->firstItem() }}-{{ $pages->lastItem() }} dari {{ $pages->total() }}</span>
        {{ $pages->links() }}
    </div>
    @endif
</div>
@endsection
