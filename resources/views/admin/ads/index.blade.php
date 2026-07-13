@extends('admin.layouts.app')
@section('title', 'Iklan')
@section('content')
<div class="page-header">
    <div>
        <h1>Iklan</h1>
        <p class="page-subtitle">Kelola iklan website</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.ads.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Iklan
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-megaphone"></i> Semua Iklan</h5>
        <span>{{ $ads->total() }} iklan</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:8%;">Gambar</th>
                    <th>Judul</th>
                    <th>Posisi</th>
                    <th>Status</th>
                    <th>Klik</th>
                    <th>Periode</th>
                    <th style="width:15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ads as $index => $ad)
                <tr>
                    <td>{{ $ads->firstItem() + $index }}</td>
                    <td>
                        <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title }}" class="thumb-table">
                    </td>
                    <td>{{ $ad->title }}</td>
                    <td>
                        <span class="badge-admin badge-admin-info">
                            @switch($ad->position)
                                @case('sidebar_top') Sidebar Atas @break
                                @case('sidebar_bottom') Sidebar Bawah @break
                                @case('in_article') Dalam Artikel @break
                                @default {{ $ad->position }}
                            @endswitch
                        </span>
                    </td>
                    <td>
                        @if($ad->is_active)
                            <span class="badge-admin badge-admin-success">Aktif</span>
                        @else
                            <span class="badge-admin badge-admin-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>{{ number_format($ad->clicks) }}</td>
                    <td>
                        @if($ad->starts_at || $ad->ends_at)
                            {{ $ad->starts_at ? $ad->starts_at->format('d M Y') : '-' }}
                            &mdash;
                            {{ $ad->ends_at ? $ad->ends_at->format('d M Y') : '-' }}
                        @else
                            <span class="text-muted">Tanpa batas</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.ads.edit', $ad->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST">
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
                    <td colspan="8">Belum ada iklan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ads->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $ads->firstItem() }}-{{ $ads->lastItem() }} dari {{ $ads->total() }}</span>
        {{ $ads->links() }}
    </div>
    @endif
</div>
@endsection
