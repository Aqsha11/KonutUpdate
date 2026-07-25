<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('pages.about') }}</loc>
        <priority>0.8</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('pages.pedoman') }}</loc>
        <priority>0.5</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('pages.privacy') }}</loc>
        <priority>0.5</priority>
        <changefreq>monthly</changefreq>
    </url>
    @foreach($categories as $category)
    <url>
        <loc>{{ route('categories.show', $category->slug) }}</loc>
        <priority>0.7</priority>
        <changefreq>daily</changefreq>
    </url>
    @endforeach
    @foreach($tags as $tag)
    <url>
        <loc>{{ route('tags.show', $tag->slug) }}</loc>
        <priority>0.5</priority>
        <changefreq>weekly</changefreq>
    </url>
    @endforeach
    @foreach($posts as $post)
    <url>
        <loc>{{ route('posts.show', $post->slug) }}</loc>
        <lastmod>{{ optional($post->updated_at ?: $post->published_at)->toIso8601String() }}</lastmod>
        <priority>0.9</priority>
        <changefreq>daily</changefreq>
    </url>
    @endforeach
</urlset>
