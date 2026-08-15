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
                    <th style="width:15%;">Gambar</th>
                    <th>Tautan</th>
                    <th>Klik</th>
                    <th style="width:15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ads as $index => $ad)
                <tr>
                    <td>{{ $ads->firstItem() + $index }}</td>
                    <td>
                        <img src="{{ Storage::url($ad->image) }}" alt="{{ $ad->title ?: 'Iklan' }}" class="thumb-table">
                    </td>
                    <td>
                        @if($ad->link)
                            <a href="{{ $ad->link }}" target="_blank" rel="nofollow" class="text-decoration-none">{{ $ad->link }}</a>
                        @else
                            <span class="text-muted">Tanpa tautan</span>
                        @endif
                    </td>
                    <td>{{ number_format($ad->clicks) }}</td>
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
                    <td colspan="5">Belum ada iklan.</td>
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
