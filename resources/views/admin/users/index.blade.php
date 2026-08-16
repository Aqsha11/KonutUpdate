@extends('admin.layouts.app')
@section('title', 'Users')
@section('content')
<div class="page-header">
    <div>
        <h1>Users</h1>
        <p class="page-subtitle">Kelola semua pengguna berdasarkan peran</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.users.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-circle"></i> Tambah User
        </a>
    </div>
</div>

@foreach($roles as $role)
@if($role->users->isNotEmpty())
<div class="table-container">
    <div class="table-header">
        <h5>
            <i class="bi bi-people"></i> {{ $role->name }}
            <span class="badge-admin badge-admin-secondary">{{ $role->users->count() }} user</span>
        </h5>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status Verifikasi</th>
                    <th>Bergabung</th>
                    <th style="width:22%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($role->users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->hasVerifiedEmail())
                        <span class="badge-admin badge-admin-success">Terverifikasi</span>
                        @else
                        <span class="badge-admin badge-admin-warning">Belum Verifikasi</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="action-btns">
                            @if(! $user->hasVerifiedEmail())
                            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-publish" title="Verifikasi Email">
                                    <i class="bi bi-patch-check"></i>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach

@if($noRole->isNotEmpty())
<div class="table-container">
    <div class="table-header">
        <h5>
            <i class="bi bi-person-dash"></i> Tanpa Role
            <span class="badge-admin badge-admin-secondary">{{ $noRole->count() }} user</span>
        </h5>
    </div>
    <div class="table-inner">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status Verifikasi</th>
                    <th>Bergabung</th>
                    <th style="width:22%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($noRole as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->hasVerifiedEmail())
                        <span class="badge-admin badge-admin-success">Terverifikasi</span>
                        @else
                        <span class="badge-admin badge-admin-warning">Belum Verifikasi</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="action-btns">
                            @if(! $user->hasVerifiedEmail())
                            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-action-publish" title="Verifikasi Email">
                                    <i class="bi bi-patch-check"></i>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
