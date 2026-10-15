@extends('admin.layouts.app')
@section('title', 'Kelola Story')
@section('content')
<div class="page-header">
    <div>
        <h1>Kelola Story</h1>
        <p class="page-subtitle">Story tampil di atas headline dan berakhir otomatis dalam {{ \App\Models\Story::EXPIRE_DAYS }} hari</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.stories.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Story
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-collection-play"></i> Daftar Story</h5>
        <span>{{ $stories->total() }} story</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:8%;">Gambar</th>
                    <th>Kategori</th>
                    <th>Link</th>
                    <th>Status</th>
                    <th>Sisa Waktu</th>
                    <th style="width:7%;">Urutan</th>
                    <th style="width:16%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stories as $index => $story)
                @php $expired = $story->isExpired(); @endphp
                <tr>
                    <td>{{ $stories->firstItem() + $index }}</td>
                    <td>
                        <img src="{{ $story->image_url }}" alt="{{ $story->display_title }}" loading="lazy" class="thumb-table">
                    </td>
                    <td>
                        <a href="{{ route('admin.stories.edit', $story->id) }}">
                            {{ $story->display_title }}
                            @unless($story->category)
                                <span class="badge-admin badge-admin-secondary" title="Kategori tidak ditemukan">tanpa kategori</span>
                            @endunless
                        </a>
                    </td>
                    <td>
                        @if($story->url)
                            <a href="{{ $story->url }}" target="_blank" rel="noopener" class="text-muted" style="font-size:0.78rem; word-break:break-all;">
                                <i class="bi bi-link-45deg"></i> {{ Str::limit($story->url, 45) }}
                            </a>
                        @else
                            <span class="text-muted">&mdash;</span>
                        @endif
                    </td>
                    <td>
                        @if(! $story->is_active)
                            <span class="badge-admin badge-admin-secondary">Nonaktif</span>
                        @elseif($expired)
                            <span class="badge-admin badge-admin-danger">Berakhir</span>
                        @else
                            <span class="badge-admin badge-admin-success">Aktif</span>
                        @endif
                    </td>
                    <td>
                        @if($expired)
                            <span class="text-danger">Berakhir</span>
                        @else
                            <span class="text-muted">{{ $story->remaining_days }} hari</span>
                        @endif
                    </td>
                    <td>{{ $story->sort_order }}</td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.stories.edit', $story->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.stories.toggle', $story->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-secondary" title="{{ $story->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="bi {{ $story->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.stories.extend', $story->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-publish" title="Perpanjang {{ \App\Models\Story::EXPIRE_DAYS }} hari">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.stories.destroy', $story->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus story ini?')">
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
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada story. Klik "Tambah Story" untuk menambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stories->hasPages())
    <div class="card-footer bg-white py-3 px-3">
        {{ $stories->links() }}
    </div>
    @endif
</div>
@endsection
