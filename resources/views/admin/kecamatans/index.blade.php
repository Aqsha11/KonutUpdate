@extends('admin.layouts.app')
@section('title', 'Kecamatan')
@section('content')
<div class="page-header">
    <div>
        <h1>Kecamatan</h1>
        <p class="page-subtitle">Kelola kecamatan di Konawe Utara</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.kecamatans.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kecamatan
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h5><i class="bi bi-geo-alt"></i> Semua Kecamatan</h5>
        <span>{{ $kecamatans->total() }} kecamatan</span>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Deskripsi</th>
                    <th>Urutan</th>
                    <th>Berita</th>
                    <th style="width:15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kecamatans as $index => $kecamatan)
                <tr>
                    <td>{{ $kecamatans->firstItem() + $index }}</td>
                    <td><strong>{{ $kecamatan->name }}</strong></td>
                    <td><code>{{ $kecamatan->slug }}</code></td>
                    <td>{{ Str::limit($kecamatan->description, 50) ?? '-' }}</td>
                    <td>{{ $kecamatan->sort_order }}</td>
                    <td><span class="badge-admin badge-admin-orange">{{ $kecamatan->posts_count ?? 0 }}</span></td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.kecamatans.edit', $kecamatan->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.kecamatans.destroy', $kecamatan->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Hapus" onclick="return confirm('Yakin hapus kecamatan ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="7">Belum ada kecamatan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kecamatans->hasPages())
    <div class="table-footer">
        <span>Menampilkan {{ $kecamatans->firstItem() }}-{{ $kecamatans->lastItem() }} dari {{ $kecamatans->total() }}</span>
        {{ $kecamatans->links() }}
    </div>
    @endif
</div>
@endsection
