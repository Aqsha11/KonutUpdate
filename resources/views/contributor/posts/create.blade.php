@extends('contributor.layouts.app')
@section('title', 'Tulis Kiriman')
@section('content')
<div class="page-header">
    <div>
        <h1>Tulis Kiriman</h1>
        <p class="page-subtitle">Buat berita atau opini untuk dikirim ke redaksi</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('kontributor.dashboard') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@include('contributor.posts._form')
@endsection
