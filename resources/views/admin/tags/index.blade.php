@extends('admin.layouts.app')
@section('title', 'Tag')
@section('content')
<div class="page-header">
    <div>
        <h1>Tag</h1>
        <p class="page-subtitle">Kelola tag berita</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.tags.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Tag
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-tag"></i> Semua Tag</h5>
        <span>{{ $tags->total() }} tag</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Berita</th>
                    <th style="width:15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tags as $index => $tag)
                <tr>
                    <td>{{ $tags->firstItem() + $index }}</td>
                    <td>{{ $tag->name }}</td>
                    <td><code>{{ $tag->slug }}</code></td>
                    <td><span class="badge-admin badge-admin-orange">{{ $tag->posts_count ?? $tag->posts->count() ?? 0 }}</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
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
                    <td colspan="5">Belum ada tag.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tags->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $tags->firstItem() }}-{{ $tags->lastItem() }} dari {{ $tags->total() }}</span>
        {{ $tags->links() }}
    </div>
    @endif
</div>
@endsection
