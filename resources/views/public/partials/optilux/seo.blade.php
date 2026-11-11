@php
    $siteName = $websiteSettings->siteName();
    $pageTitle = trim($__env->yieldContent('title', 'Beranda'));
    $seoTitle = trim($__env->yieldContent('meta_title', $pageTitle.' — '.$siteName));
    $seoDescription = trim($__env->yieldContent(
        'meta_description',
        $websiteSettings->site_tagline ?: 'Website resmi '.$siteName
    ));
    $seoImage = trim($__env->yieldContent('meta_image', '')) ?: $websiteSettings->defaultShareImageUrl();
    $seoUrl = trim($__env->yieldContent('meta_url', url()->current()));
    $seoType = trim($__env->yieldContent('meta_type', 'website'));
    $seoRobots = trim($__env->yieldContent('meta_robots', 'index,follow'));
@endphp
<title>{{ $seoTitle }}</title>
<link rel="canonical" href="{{ $seoUrl }}">
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<meta name="author" content="{{ $siteName }}">

<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $pageTitle }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

@stack('seo')
