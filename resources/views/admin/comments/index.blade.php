@extends('admin.layouts.app')
@section('title', 'Moderasi Komentar')
@section('content')
<div class="page-header">
    <div>
        <h1>Moderasi Komentar</h1>
        <p class="page-subtitle">Kelola komentar dari pembaca</p>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--primary-light); color: var(--primary);"><i class="bi bi-chat-dots"></i></div>
        <div class="stat-info">
            <div class="stat-number">{{ $comments->total() }}</div>
            <div class="stat-label">Total Komentar</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fff3cd; color: #f59e0b;"><i class="bi bi-clock"></i></div>
        <div class="stat-info">
            <div class="stat-number">{{ $pendingCount }}</div>
            <div class="stat-label">Menunggu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #d1e7dd; color: #198754;"><i class="bi bi-check-circle"></i></div>
        <div class="stat-info">
            <div class="stat-number">{{ $approvedCount }}</div>
            <div class="stat-label">Disetujui</div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-funnel"></i> Filter</h5>
    </div>
    <div class="table-inner">
        <form class="filter-bar" method="GET" action="{{ route('admin.comments.index') }}">
            <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
            </select>
            <input type="text" class="form-control" name="search" placeholder="Cari nama, email, atau isi komentar..." value="{{ request('search') }}">
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-chat-left-text"></i> Semua Komentar</h5>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th>Penulis</th>
                    <th>Komentar</th>
                    <th>Artikel</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th style="width:14%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $index => $comment)
                <tr>
                    <td>{{ $comments->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-semibold">{{ $comment->name }}</div>
                        <div class="text-muted" style="font-size:0.8rem;">{{ $comment->email }}</div>
                    </td>
                    <td>
                        <div style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $comment->body }}</div>
                    </td>
                    <td>
                        <a href="{{ route('admin.posts.edit', $comment->post_id) }}" style="font-size:0.85rem;">
                            {{ Str::limit($comment->post->title ?? '-', 40) }}
                        </a>
                    </td>
                    <td>
                        @if($comment->is_approved)
                            <span class="badge-admin badge-admin-success">Disetujui</span>
                        @else
                            <span class="badge-admin badge-admin-warning">Menunggu</span>
                        @endif
                    </td>
                    <td>{{ $comment->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <div class="action-btns">
                            @if(!$comment->is_approved)
                            <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-publish" title="Setujui">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-draft" title="Tolak">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST">
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
                    <td colspan="7">Belum ada komentar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($comments->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $comments->firstItem() }}-{{ $comments->lastItem() }} dari {{ $comments->total() }}</span>
        {{ $comments->links() }}
    </div>
    @endif
</div>
@endsection
