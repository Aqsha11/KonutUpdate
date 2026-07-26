@extends('admin.layouts.app')
@section('title', 'Daftar Berita')
@section('content')
<div class="page-header">
    <div>
        <h1>Daftar Berita</h1>
        <p class="page-subtitle">Kelola semua berita portal</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.posts.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Berita
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-funnel"></i> Filter</h5>
    </div>
    <div class="table-inner">
        <form class="filter-bar" method="GET" action="{{ route('admin.posts.index') }}">
            <select class="form-select" name="category" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories ?? [] as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="form-select" name="kecamatan" onchange="this.form.submit()">
                <option value="">Semua Kecamatan</option>
                @foreach($kecamatans ?? [] as $kecamatan)
                <option value="{{ $kecamatan->id }}" {{ request('kecamatan') == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->name }}</option>
                @endforeach
            </select>
            <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <select class="form-select" name="type" onchange="this.form.submit()">
                <option value="">Semua Jenis</option>
                <option value="article" {{ request('type') == 'article' ? 'selected' : '' }}>Artikel</option>
                <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
            </select>
            <input type="text" class="form-control" name="search" placeholder="Cari berita..." value="{{ request('search') }}">
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-newspaper"></i> Semua Berita</h5>
        <span>{{ $posts->total() }} berita</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:6%;">Thumb</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Kecamatan</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Tanggal</th>
                    <th style="width:18%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $index => $post)
                <tr>
                    <td>{{ $posts->firstItem() + $index }}</td>
                    <td>
                        @if($post->thumbnail)
                        <img src="{{ Storage::url($post->thumbnail) }}" alt="{{ $post->title }}" class="thumb-table">
                        @elseif($post->isVideo() && $post->video_poster)
                        <img src="{{ $post->video_poster }}" alt="{{ $post->title }}" class="thumb-table" onerror="this.parentElement.innerHTML='<div class=\'thumb-table bg-dark d-flex align-items-center justify-content-center text-white\'><i class=\'bi bi-play-circle fs-4\'></i></div>'">
                        @elseif($post->isVideo())
                        <div class="thumb-table bg-dark d-flex align-items-center justify-content-center text-white"><i class="bi bi-play-circle fs-4"></i></div>
                        @else
                        <div class="thumb-table bg-secondary d-flex align-items-center justify-content-center text-white"><i class="bi bi-image fs-4"></i></div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.posts.edit', $post->id) }}">
                            {{ $post->title }}
                        </a>
                        @if($post->is_breaking)
                        <span class="badge-admin badge-admin-danger ms-1">Breaking</span>
                        @endif
                        @if($post->is_headline)
                        <span class="badge-admin badge-admin-info ms-1">Headline</span>
                        @endif
                        @if($post->isVideo())
                        <span class="badge-admin badge-admin-orange ms-1"><i class="bi bi-play-circle"></i> Video</span>
                        @endif
                    </td>
                    <td>
                        @if($post->categories->count() > 0)
                            @foreach($post->categories as $cat)
                                <span class="badge-admin badge-admin-info">{{ $cat->name }}</span>
                            @endforeach
                        @elseif($post->category)
                            <span class="badge-admin badge-admin-info">{{ $post->category->name }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $post->kecamatan ? $post->kecamatan->name : '-' }}</td>
                    <td>{{ $post->author->name ?? '-' }}</td>
                    <td>
                        @if($post->status === 'published')
                        <span class="badge-admin badge-admin-success">Published</span>
                        @else
                        <span class="badge-admin badge-admin-warning">Draft</span>
                        @endif
                    </td>
                    <td>{{ number_format($post->views_count) }}</td>
                    <td>{{ $post->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @if($post->status === 'draft')
                            <form action="{{ route('admin.posts.publish', $post->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-publish" title="Publikasi">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.posts.draft', $post->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-draft" title="Draft">
                                    <i class="bi bi-archive"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="10">Belum ada berita.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $posts->firstItem() }}-{{ $posts->lastItem() }} dari {{ $posts->total() }}</span>
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
