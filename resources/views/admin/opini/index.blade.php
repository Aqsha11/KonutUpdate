@extends('admin.layouts.app')
@section('title', 'Kelola Opini')
@section('content')
<div class="page-header">
    <div>
        <h1>Kelola Opini</h1>
        <p class="page-subtitle">Tulis dan kelola opini redaksi</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.opini.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tulis Opini
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-funnel"></i> Filter</h5>
    </div>
    <div class="table-inner">
        <form class="filter-bar" method="GET" action="{{ route('admin.opini.index') }}">
            <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select class="form-select" name="category" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <input type="text" class="form-control" name="search" placeholder="Cari opini..." value="{{ request('search') }}">
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-chat-quote"></i> Daftar Opini</h5>
        <span>{{ $posts->total() }} opini</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:6%;">Thumb</th>
                    <th>Judul</th>
                    <th>Kategori</th>
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
                        @else
                        <div class="thumb-table bg-secondary d-flex align-items-center justify-content-center text-white"><i class="bi bi-chat-quote fs-4"></i></div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.opini.edit', $post->id) }}">
                            {{ $post->title }}
                        </a>
                        @if($post->is_headline)
                        <span class="badge-admin badge-admin-info ms-1">Headline</span>
                        @endif
                        @if($post->is_featured)
                        <span class="badge-admin badge-admin-success ms-1">Konten Pilihan</span>
                        @endif
                        @if($post->rejection_reason)
                        <div class="mt-1 text-danger" style="font-size:0.78rem;" title="{{ $post->rejection_reason }}">
                            <i class="bi bi-exclamation-circle"></i> Alasan: {{ Str::limit($post->rejection_reason, 60) }}
                        </div>
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
                    <td>{{ $post->author_name }}</td>
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
                    <td>{{ number_format($post->views_count) }}</td>
                    <td>{{ $post->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.opini.edit', $post->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.opini.destroy', $post->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @if($post->status === 'pending')
                            <form action="{{ route('admin.opini.approve', $post->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-publish" title="Setujui & Tayangkan">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <button type="button" class="btn-action btn-action-danger" title="Tolak" data-reject-url="{{ route('admin.opini.reject', $post->id) }}" data-reject-title="{{ $post->title }}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                            @if($post->status === 'draft')
                            <form action="{{ route('admin.opini.publish', $post->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-publish" title="Publikasi">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.opini.draft', $post->id) }}" method="POST">
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
                    <td colspan="9">Belum ada opini.</td>
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

{{-- Modal Tolak Kiriman --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="#" method="POST" id="rejectForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-1"></i> Tolak Opini</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" style="font-size:0.85rem;">Alasan penolakan akan ditampilkan kepada penulis agar dapat memperbaiki kirimannya.</p>
                    <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="required">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" class="form-control" required maxlength="1000" placeholder="Contoh: Judul tidak sesuai kaidah jurnalistik, sumber tidak jelas, dsb."></textarea>
                    @if ($errors->has('rejection_reason'))
                    <div class="invalid-feedback d-block">{{ $errors->first('rejection_reason') }}</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-admin btn-admin-danger"><i class="bi bi-x-lg"></i> Tolak Opini</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('rejectModal');
        if (!modalEl) return;
        var modal = new bootstrap.Modal(modalEl);
        var form = document.getElementById('rejectForm');

        document.querySelectorAll('[data-reject-url]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                form.action = btn.dataset.rejectUrl;
                document.getElementById('rejection_reason').value = '';
                document.getElementById('rejection_reason').classList.remove('is-invalid');
                modal.show();
            });
        });
    });
</script>
@endpush
@endsection
