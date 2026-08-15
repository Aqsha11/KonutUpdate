@extends('contributor.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-header">
    <div>
        <h1>Halo, {{ auth()->user()->name }}!</h1>
        <p class="page-subtitle">Kelola kiriman berita atau opini Anda. Kiriman akan tayang setelah diverifikasi admin.</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('kontributor.posts.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tulis Kiriman
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">Menunggu Verifikasi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['published'] }}</div>
                <div class="stat-label">Sudah Tayang</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-pencil"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['draft'] }}</div>
                <div class="stat-label">Draft</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card red">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['rejected'] }}</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-newspaper"></i> Kiriman Saya</h5>
        <span>{{ $posts->total() }} kiriman</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Judul</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th style="width:18%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $index => $post)
                <tr>
                    <td>{{ $posts->firstItem() + $index }}</td>
                    <td>
                        <a href="{{ route('kontributor.posts.edit', $post->id) }}" class="contributor-post-title">
                            {{ $post->title }}
                        </a>
                        @if($post->rejection_reason)
                        <div class="contributor-reject-reason">
                            <i class="bi bi-exclamation-circle"></i> {{ $post->rejection_reason }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge-admin {{ $post->type === 'opini' ? 'badge-admin-warning' : 'badge-admin-info' }}">
                            {{ $post->type === 'opini' ? 'Opini' : 'Berita' }}
                        </span>
                    </td>
                    <td>
                        @if($post->status === 'published')
                        <span class="badge-admin badge-admin-success">Published</span>
                        @elseif($post->status === 'pending')
                        <span class="badge-admin badge-admin-warning">Menunggu Verifikasi</span>
                        @elseif($post->status === 'rejected')
                        <span class="badge-admin badge-admin-danger">Ditolak</span>
                        @else
                        <span class="badge-admin badge-admin-secondary">Draft</span>
                        @endif
                    </td>
                    <td>{{ $post->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="action-btns">
                            @if($post->status !== 'published')
                            <a href="{{ route('kontributor.posts.edit', $post->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('kontributor.posts.destroy', $post->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @else
                            <a href="{{ route('posts.show', $post->slug) }}" class="btn-action btn-action-view" title="Lihat" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="6">Belum ada kiriman. Mulai tulis kiriman pertama Anda!</td>
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
