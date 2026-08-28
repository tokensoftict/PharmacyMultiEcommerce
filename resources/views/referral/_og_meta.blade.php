{{--
    ── Open Graph + Twitter Card + Standard SEO meta tags for Referral Links ─────
--}}

{{-- ── Standard SEO ───────────────────────────────────────────────────── --}}
<title>{{ $viewModel->title }}</title>
<meta name="description"    content="{{ $viewModel->ogDescription }}">
<meta name="keywords"       content="General Drug Centre, PS GDC, referral, online pharmacy, supermarket, Nigeria, discounts, loyalty points">
<meta name="robots"         content="index, follow">
<link rel="canonical"       href="{{ $viewModel->canonicalUrl }}">

{{-- ── Open Graph (Facebook, WhatsApp, LinkedIn, Telegram, iMessage) ──────── --}}
<meta property="og:type"        content="website">
<meta property="og:title"       content="{{ $viewModel->title }}">
<meta property="og:description" content="{{ $viewModel->ogDescription }}">
<meta property="og:image"       content="{{ $viewModel->image }}">
<meta property="og:image:secure_url" content="{{ $viewModel->image }}">
<meta property="og:image:type"  content="image/png">
<meta property="og:image:alt"   content="PS General Drugs Centre Logo">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">
<meta property="og:url"         content="{{ $viewModel->url }}">
<meta property="og:site_name"   content="{{ $viewModel->siteName }}">
<meta property="og:locale"      content="{{ $viewModel->locale }}">

{{-- ── Twitter / X Card ────────────────────────────────────────────────── --}}
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:site"        content="@generaldrugcentre">
<meta name="twitter:creator"     content="@generaldrugcentre">
<meta name="twitter:title"       content="{{ $viewModel->title }}">
<meta name="twitter:description" content="{{ $viewModel->ogDescription }}">
<meta name="twitter:image"       content="{{ $viewModel->image }}">
<meta name="twitter:image:alt"   content="PS General Drugs Centre Logo">

{{-- ── Theme & App Meta ───────────────────────────────────────────────── --}}
<meta name="theme-color" content="#D32F2F">
<meta name="application-name" content="{{ $viewModel->siteName }}">
