@extends('contributor.layouts.app')
@section('title', 'Edit Kiriman')
@section('content')
<div class="page-header">
    <div>
        <h1>Edit Kiriman</h1>
        <p class="page-subtitle">Perbaiki kiriman lalu kirim ulang untuk verifikasi</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('kontributor.dashboard') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@include('contributor.posts._form')
@endsection
