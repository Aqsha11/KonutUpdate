{{--
    Sitemap Google News (news sitemap).

    Hanya memuat artikel yang terbit dalam 2 hari terakhir (syarat Google News),
    maksimal 1.000 URL, dan tanpa artikel duplikat.
--}}
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    @foreach($posts as $post)
    <url>
        <loc>{{ route('posts.show', $post->slug) }}</loc>
        <news:news>
            <news:publication>
                <news:name>KonutUpdate</news:name>
                <news:language>id</news:language>
            </news:publication>
            <news:publication_date>{{ optional($post->published_at)->toAtomString() }}</news:publication_date>
            <news:title>{{ \Illuminate\Support\Str::limit($post->title, 110) }}</news:title>
            @if($post->categories->isNotEmpty())
                <news:keywords>{{ $post->categories->pluck('name')->implode(', ') }}</news:keywords>
            @endif
        </news:news>
    </url>
    @endforeach
</urlset>
