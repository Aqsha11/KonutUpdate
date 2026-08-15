@php
    $secData = $kecamatanData ?? ['hero' => null, 'trending' => collect(), 'latest' => collect()];

    $secPosts = collect()
        ->push($secData['hero'])
        ->merge($secData['trending'] ?? collect())
        ->merge($secData['latest'] ?? collect())
        ->filter()
        ->unique('id')
        ->values()
        ->take(8);
@endphp

@if($secPosts->count() > 0)
<section class="mb-3 lg:mb-4">
    <div class="section-bar">
        <h2 class="section-bar-title"><span class="section-bar-dot bg-accent"></span>Kec. {{ $kecamatanName }}</h2>
        <a href="{{ route('kecamatan.show', $kecamatanSlug) }}" class="section-bar-link">Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i></a>
    </div>
    <div class="ku-cat-row hide-scrollbar">
        @foreach($secPosts as $post)
        <article class="ku-cat-post" data-post-id="{{ $post->id }}">
            <div class="ku-cat-left">
                <h3 class="ku-cat-title"><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h3>
                <div class="ku-cat-author">
                    <div class="ku-cat-avatar">
                        @if($post->author && $post->author->avatar)
                        <img src="{{ Storage::url($post->author->avatar) }}" alt="{{ $post->author->name }}">
                        @else
                        <span>{{ strtoupper(substr($post->author_name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <span class="ku-cat-author-name">{{ $post->author_name }}</span>
                </div>
                <div class="ku-cat-actions">
                    <button type="button" onclick="toggleLike({{ $post->id }})" data-like-btn="{{ $post->id }}" class="ku-cat-act {{ $post->isLikedBy(request()->ip()) ? 'liked' : '' }}">
                        <i data-lucide="heart" class="w-3 h-3"></i>
                        <span data-like-count="{{ $post->id }}">{{ $post->likesCount() }}</span>
                    </button>
                    <a href="{{ route('posts.show', $post->slug) }}#comments" class="ku-cat-act">
                        <i data-lucide="message-circle" class="w-3 h-3"></i>
                        <span>{{ $post->commentsCount() }}</span>
                    </a>
                    <button type="button" onclick="sharePost('{{ route('posts.show', $post->slug) }}', '{{ addslashes($post->title) }}')" class="ku-cat-act">
                        <i data-lucide="share-2" class="w-3 h-3"></i>
                    </button>
                    <time class="ku-cat-date">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d/m/Y') : '' }}</time>
                </div>
            </div>
            <div class="ku-cat-right">
                <a href="{{ route('posts.show', $post->slug) }}">
                    <img src="{{ postThumbnail($post) }}" alt="{{ $post->title }}" loading="lazy">
                </a>
            </div>
        </article>
        @endforeach
    </div>
</section>
@endif
