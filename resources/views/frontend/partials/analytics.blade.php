@php
    $gaMeasurementId = trim((string) ($site_settings['google_analytics_id'] ?? ''));
@endphp
@if($gaMeasurementId !== '')
<script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($gaMeasurementId) }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', @json($gaMeasurementId));
</script>
@endif
