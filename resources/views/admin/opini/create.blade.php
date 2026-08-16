@extends('admin.layouts.app')
@section('title', 'Tulis Opini')
@section('content')
<div class="page-header">
    <div>
        <h1>Tulis Opini</h1>
        <p class="page-subtitle">Buat opini redaksi baru</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.opini.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@include('admin.opini._form')
@endsection
