@extends('admin.layouts.app')
@section('title', 'Kelola Video')
@section('content')
<div class="page-header">
    <div>
        <h1>Kelola Video</h1>
        <p class="page-subtitle">Kelola video TikTok yang ditayangkan di situs</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.videos.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Video
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-funnel"></i> Filter</h5>
    </div>
    <div class="table-inner">
        <form class="filter-bar" method="GET" action="{{ route('admin.videos.index') }}">
            <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <input type="text" class="form-control" name="search" placeholder="Cari judul atau link..." value="{{ request('search') }}">
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-play-btn"></i> Daftar Video</h5>
        <span>{{ $posts->total() }} video</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:5%;">Play</th>
                    <th>Judul & Link TikTok</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Tanggal</th>
                    <th style="width:14%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $index => $post)
                <tr>
                    <td>{{ $posts->firstItem() + $index }}</td>
                    <td>
                        <a href="{{ $post->video_url }}" target="_blank" rel="noopener" class="thumb-table bg-dark d-block text-decoration-none" title="Buka di TikTok">
                            @if($post->thumbnail)
                            <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                            @else
                            <span class="d-flex align-items-center justify-content-center h-100 text-white"><i class="bi bi-tiktok fs-4"></i></span>
                            @endif
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('admin.videos.edit', $post->id) }}">{{ $post->title }}</a>
                        <div class="text-muted mt-1" style="font-size:0.75rem; word-break:break-all;">
                            <i class="bi bi-link-45deg"></i> {{ Str::limit($post->video_url, 60) }}
                        </div>
                    </td>
                    <td>
                        @if($post->categories->count() > 0)
                            <span class="badge-admin badge-admin-info">{{ $post->categories->first()->name }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($post->status === 'published')
                        <span class="badge-admin badge-admin-success">Published</span>
                        @else
                        <span class="badge-admin badge-admin-secondary">Draft</span>
                        @endif
                    </td>
                    <td>{{ number_format($post->views_count) }}</td>
                    <td>{{ $post->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.videos.edit', $post->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($post->status === 'draft')
                            <form action="{{ route('admin.videos.publish', $post->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-publish" title="Publikasi">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.videos.draft', $post->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-secondary" title="Jadikan Draft">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.videos.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus video ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada video. Klik "Tambah Video" untuk menambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div class="card-footer bg-white py-3 px-3">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
