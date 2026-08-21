<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Homepage --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ $lastmod->toAtomString() }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Halaman statis --}}
    <url>
        <loc>{{ route('pages.about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{{ route('pages.pedoman') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('pages.privacy') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>{{ route('pages.kontak') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    {{-- Hub Kecamatan (13 kecamatan Konawe Utara) --}}
    @foreach($kecamatans as $kecamatan)
    <url>
        <loc>{{ route('kecamatan.show', $kecamatan->slug) }}</loc>
        @if($kecamatan->posts_max_published_at)
            <lastmod>{{ \Carbon\Carbon::parse($kecamatan->posts_max_published_at)->toAtomString() }}</lastmod>
        @endif
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Arsip Kategori --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ route('categories.show', $category->slug) }}</loc>
        @if($category->posts_max_published_at)
            <lastmod>{{ \Carbon\Carbon::parse($category->posts_max_published_at)->toAtomString() }}</lastmod>
        @endif
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- Hanya tag dengan >= 2 artikel terbit (hindari thin content) --}}
    @foreach($tags as $tag)
    <url>
        <loc>{{ route('tags.show', $tag->slug) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.4</priority>
    </url>
    @endforeach

    {{-- Artikel --}}
    @foreach($posts as $post)
    <url>
        <loc>{{ route('posts.show', $post->slug) }}</loc>
        <lastmod>{{ optional($post->updated_at ?: $post->published_at)->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    @endforeach
</urlset>
