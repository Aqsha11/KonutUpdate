<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>{{ $site_settings['site_name'] ?? 'Konut.Update' }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ $site_settings['description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya' }}</description>
        <language>id</language>
        <lastBuildDate>{{ $posts->first() ? $posts->first()->published_at->toRfc2822String() : now()->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ url('/feed') }}" rel="self" type="application/rss+xml"/>
        <image>
            <url>{{ !empty($site_settings['logo']) ? url(Storage::url($site_settings['logo'])) : url('/') }}</url>
            <title>{{ $site_settings['site_name'] ?? 'Konut.Update' }}</title>
            <link>{{ url('/') }}</link>
        </image>
        @foreach($posts as $post)
        <item>
            <title><![CDATA[{{ $post->title }}]]></title>
            <link>{{ route('posts.show', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('posts.show', $post->slug) }}</guid>
            <description><![CDATA[{{ $post->excerpt ?: strip_tags(Str::limit($post->body, 300)) }}]]></description>
            <category>{{ $post->category->name ?? 'Umum' }}</category>
            <dc:creator><![CDATA[{{ $post->author->name ?? 'Redaksi' }}]]></dc:creator>
            <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
            @if($post->thumbnail)
            <media:thumbnail url="{{ url(Storage::url($post->thumbnail)) }}" width="800" height="450"/>
            <media:content url="{{ url(Storage::url($post->thumbnail)) }}" medium="image" width="800" height="450"/>
            @endif
            @if($post->isVideo() && $post->video_url)
            <media:content url="{{ $post->video_url }}" medium="video" type="text/html"/>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
