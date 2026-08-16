@extends('admin.layouts.app')
@section('title', 'Edit Opini')
@section('content')
<div class="page-header">
    <div>
        <h1>Edit Opini</h1>
        <p class="page-subtitle">Perbarui opini redaksi</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.opini.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@include('admin.opini._form', ['post' => $post])
@endsection
