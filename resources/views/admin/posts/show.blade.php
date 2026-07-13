@extends('admin.layouts.app')
@section('title', $post->title)
@section('content')
<div class="page-header">
    <div>
        <h1>Detail Berita</h1>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.posts.index') }}" class="btn-admin btn-admin-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card-admin">
    <div class="card-admin-body">
        @if($post->thumbnail)
        <div class="mb-4">
            <img src="{{ Storage::url($post->thumbnail) }}" alt="{{ $post->title }}" class="img-fluid rounded" style="max-height:400px;width:100%;object-fit:cover;border-radius:12px;">
        </div>
        @endif
        <h2 class="fw-bold mb-3" style="font-size:1.5rem;">{{ $post->title }}</h2>
        <div class="d-flex flex-wrap gap-3 mb-4">
            <span><i class="bi bi-person me-1"></i>{{ $post->author->name ?? '-' }}</span>
            <span><i class="bi bi-tag me-1"></i>{{ $post->category->name ?? '-' }}</span>
            <span><i class="bi bi-calendar me-1"></i>{{ $post->created_at->format('d M Y H:i') }}</span>
            <span><i class="bi bi-eye me-1"></i>{{ number_format($post->views ?? $post->views_count) }} views</span>
            @if($post->status === 'published')
            <span class="badge-admin badge-admin-success">Published</span>
            @else
            <span class="badge-admin badge-admin-warning">Draft</span>
            @endif
        </div>
        @if($post->excerpt)
        <div class="mb-4 p-3 excerpt-box">
            <strong>Ringkasan:</strong>
            <p class="mb-0 mt-1">{{ $post->excerpt }}</p>
        </div>
        @endif
        <hr>
        <div class="post-body">
            {!! $post->body !!}
        </div>
        @if($post->tags->count() > 0)
        <div class="mt-4 pt-3 tags-section">
            <strong>Tags:</strong>
            @foreach($post->tags as $tag)
            <span class="badge-admin badge-admin-secondary ms-1">{{ $tag->name }}</span>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
