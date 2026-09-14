@php
    $schemaName = $site_settings['site_name'] ?? 'KonutUpdate';
    $schemaTagline = $site_settings['tagline'] ?? 'Daily News & Update Konawe Utara';
    $schemaDesc = $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya';
    $schemaLogo = !empty($site_settings['logo'])
        ? url(Storage::url($site_settings['logo']))
        : url('/icons/favicon.png');
    $schemaSameAs = collect(['facebook', 'instagram', 'youtube', 'tiktok'])
        ->map(fn ($k) => $site_settings[$k] ?? null)
        ->filter()
        ->values()
        ->all();
@endphp
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": ["Organization", "NewsMediaOrganization"],
    "@@id": "{{ url('#organization') }}",
    "name": @json($schemaName),
    "alternateName": ["KonutUpdate", "Konut Update", "Konut"],
    "url": @json(url('/')),
    "description": @json($schemaDesc),
    "slogan": @json($schemaTagline),
    "logo": {
        "@@type": "ImageObject",
        "url": @json($schemaLogo)
    },
    "sameAs": @json($schemaSameAs),
    "contactPoint": {
        "@@type": "ContactPoint",
        "contactType": "newsroom",
        "telephone": @json($site_settings['phone'] ?? ''),
        "email": @json($site_settings['email'] ?? '')
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "@@id": "{{ url('#website') }}",
    "url": @json(url('/')),
    "name": @json($schemaName),
    "publisher": { "@@id": "{{ url('#organization') }}" },
    "potentialAction": {
        "@@type": "SearchAction",
        "target": {
            "@@type": "EntryPoint",
            "urlTemplate": @json(route('search') . '?q={search_term_string}')
        },
        "query-input": "required name=search_term_string"
    }
}
</script>