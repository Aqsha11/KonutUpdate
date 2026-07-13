@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-header">
    <div>
        <h1>Selamat Datang, {{ auth()->user()->name }}</h1>
        <p class="page-subtitle">{{ now()->translatedFormat('l, d F Y') }} &middot; Ringkasan portal berita Anda</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.posts.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Buat Berita
        </a>
    </div>
</div>

<div class="row g-4 stat-row">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card orange animate-fade-in animate-fade-in-d1">
            <div class="stat-icon">
                <i class="bi bi-newspaper"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Berita</div>
                <div class="stat-value">{{ number_format($totalPosts) }}</div>
                <div class="stat-desc">
                    <span class="stat-badge neutral"><i class="bi bi-check-circle me-1"></i>{{ number_format($publishedPosts) }} Publikasi</span>
                    <span class="stat-badge info"><i class="bi bi-pencil me-1"></i>{{ number_format($draftPosts) }} Draft</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card green animate-fade-in animate-fade-in-d2">
            <div class="stat-icon">
                <i class="bi bi-tags"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Kategori</div>
                <div class="stat-value">{{ number_format($totalCategories) }}</div>
                <div class="stat-desc">
                    <span class="stat-badge info"><i class="bi bi-folder me-1"></i>Kelola kategori</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card blue animate-fade-in animate-fade-in-d3">
            <div class="stat-icon">
                <i class="bi bi-eye"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Views</div>
                <div class="stat-value">{{ number_format($totalPageViews) }}</div>
                <div class="stat-desc">
                    <span class="stat-badge up"><i class="bi bi-graph-up me-1"></i>Total kunjungan</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card red animate-fade-in animate-fade-in-d4">
            <div class="stat-icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Hari Ini</div>
                <div class="stat-value">{{ number_format($postsPublishedToday) }}</div>
                <div class="stat-desc">
                    <span class="stat-badge neutral"><i class="bi bi-clock me-1"></i>Berita terbit hari ini</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 dashboard-bottom">
    <div class="col-md-7">
        <div class="table-container">
            <div class="table-header">
                <h5><i class="bi bi-clock-history"></i> Berita Terbaru</h5>
                <div class="table-actions">
                    <a href="{{ route('admin.posts.index') }}" class="btn-admin btn-admin-sm btn-admin-ghost">
                        Lihat Semua <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="table-inner">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width:50%;">Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th class="text-end">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPosts as $post)
                        <tr>
                            <td>
                                <a href="{{ route('admin.posts.edit', $post->id) }}">{{ $post->title }}</a>
                            </td>
                            <td>
                                @if($post->category)
                                    <span class="badge-admin badge-admin-orange">{{ $post->category->name }}</span>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                @if($post->published_at)
                                    <span class="badge-admin badge-admin-success">Published</span>
                                @else
                                    <span class="badge-admin badge-admin-warning">Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ $post->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="4">Belum ada berita.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="table-container">
            <div class="table-header">
                <h5><i class="bi bi-fire"></i> Berita Terpopuler</h5>
            </div>
            <div class="table-inner">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th class="text-end">Dilihat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($popularPosts as $post)
                        <tr>
                            <td>
                                <a href="{{ route('admin.posts.edit', $post->id) }}">{{ $post->title }}</a>
                            </td>
                            <td class="text-end">
                                <span class="badge-admin badge-admin-orange">{{ number_format($post->views_count) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="2">Belum ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-admin animate-fade-in">
            <div class="card-admin-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Pengguna</h5>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Total pengguna terdaftar</span>
                    <span class="fw-bold">{{ number_format($totalUsers) }}</span>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn-admin btn-admin-sm btn-admin-secondary w-100 justify-content-center">
                    <i class="bi bi-person-gear"></i> Kelola Pengguna
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
