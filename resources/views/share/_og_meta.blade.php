{{--
    ── Open Graph + Twitter Card + Standard SEO meta tags ──────────────────
    Included inside <head> by share/product.blade.php.
    All values come from the ProductShareViewModel — no DB calls here.
--}}

{{-- ── Standard SEO ───────────────────────────────────────────────────── --}}
<title>{{ $viewModel->title }} | {{ $viewModel->siteName }}</title>
<meta name="description"    content="{{ $viewModel->ogDescription() }}">
<meta name="keywords"       content="{{ implode(', ', array_filter([
    $viewModel->title,
    $viewModel->brand,
    $viewModel->category,
    'generaldrugcentre.com',
    'online pharmacy',
    'Nigeria',
])) }}">
<meta name="robots"         content="index, follow">
<link rel="canonical"       href="{{ $viewModel->canonicalUrl }}">

{{-- ── Open Graph (Facebook, WhatsApp, LinkedIn, Telegram) ─────────────── --}}
<meta property="og:type"        content="product">
<meta property="og:title"       content="{{ $viewModel->title }}">
<meta property="og:description" content="{{ $viewModel->ogDescription() }}">
<meta property="og:image"       content="{{ $viewModel->image }}">
<meta property="og:image:alt"   content="{{ $viewModel->title }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url"         content="{{ $viewModel->url }}">
<meta property="og:site_name"   content="{{ $viewModel->siteName }}">
<meta property="og:locale"      content="{{ $viewModel->locale }}">

@if($viewModel->price !== null)
<meta property="product:price:amount"   content="{{ number_format($viewModel->price, 2) }}">
<meta property="product:price:currency" content="{{ $viewModel->currency }}">
@endif

{{-- ── Twitter / X Card ────────────────────────────────────────────────── --}}
{{-- "summary_large_image" renders a full-width preview image on X/Twitter --}}
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:site"        content="@generaldrugcentre">
<meta name="twitter:creator"     content="@generaldrugcentre">
<meta name="twitter:title"       content="{{ $viewModel->title }}">
<meta name="twitter:description" content="{{ $viewModel->ogDescription() }}">
<meta name="twitter:image"       content="{{ $viewModel->image }}">
<meta name="twitter:image:alt"   content="{{ $viewModel->title }}">

{{-- ── WhatsApp-specific (uses OG tags above, but needs exact HTTPS image) --}}
{{-- WhatsApp crawls og:image — ensure the URL is always HTTPS. ─────────── --}}

{{-- ── Theme colour for mobile browsers ─────────────────────────────────── --}}
<meta name="theme-color" content="#D32F2F">
<meta name="application-name" content="{{ $viewModel->siteName }}">
