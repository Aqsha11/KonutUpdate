{{--
    Sumber tunggal metadata SEO halaman.

    Semua halaman (home, artikel, kategori, tag, kecamatan, arsip, halaman
    statis, arsip video/opini, profil) memakai partial ini lewat layout
    `frontend.layouts.app`, sehingga tag OG/Twitter/robots/description selalu
    lengkap dan tidak pernah dobel.

    Nilai per halaman disuplai lewat section:
      @section('seo_title')        judul untuk og:title & twitter:title (tag <title> memakai @section('title'))
      @section('seo_description')  deskripsi meta
      @section('seo_type')         og:type (website / article / video.other)
      @section('seo_robots')      (meta robots, default: max-image-preview:large, ...)
      @section('seo_image')        URL og:image / twitter:image
      @section('seo_image_alt')    alt text gambar
      @section('seo_image_width')  lebar gambar (default 1200)
      @section('seo_image_height') tinggi gambar (default 630)
      @section('seo_extra')        tag tambahan (article:published_time, dll)
--}}
@php
    $siteName = $site_settings['site_name'] ?? 'KonutUpdate';
    $defaultDescription = $site_settings['meta_description'] ?? 'Portal berita terkini Konawe Utara - Informasi cepat dan terpercaya';
    $defaultImage = !empty($site_settings['logo']) ? url(Storage::url($site_settings['logo'])) : url('/og-default.jpg');

    $seoTitle = seoSectionValue('seo_title') ?: (seoSectionValue('title') ?: $siteName);
    $seoDescription = seoSectionValue('seo_description') ?: $defaultDescription;
    $seoType = seoSectionValue('seo_type') ?: 'website';
    $seoRobots = seoSectionValue('seo_robots') ?: 'max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $seoImage = seoSectionValue('seo_image') ?: $defaultImage;
    $seoImageAlt = seoSectionValue('seo_image_alt') ?: $seoTitle;
    $seoImageWidth = seoSectionValue('seo_image_width') ?: '1200';
    $seoImageHeight = seoSectionValue('seo_image_height') ?: '630';
    $seoUrl = $canonical ?? url()->current();
@endphp
<meta name="description" content="{{ $seoDescription }}" />
<meta name="robots" content="{{ $seoRobots }}" />
<meta property="og:site_name" content="{{ $siteName }}" />
<meta property="og:locale" content="id_ID" />
<meta property="og:type" content="{{ $seoType }}" />
<meta property="og:url" content="{{ $seoUrl }}" />
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:description" content="{{ $seoDescription }}" />
<meta property="og:image" content="{{ $seoImage }}" />
<meta property="og:image:width" content="{{ $seoImageWidth }}" />
<meta property="og:image:height" content="{{ $seoImageHeight }}" />
<meta property="og:image:alt" content="{{ $seoImageAlt }}" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
<meta name="twitter:description" content="{{ $seoDescription }}" />
<meta name="twitter:image" content="{{ $seoImage }}" />
@stack('seo_extra')
