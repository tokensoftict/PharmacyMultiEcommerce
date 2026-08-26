@extends('share.layout')

{{-- ── SEO / OG meta ──────────────────────────────────────────────────────── --}}
@section('meta')
    @include('share._og_meta')
@endsection

{{-- ── Page title (also set inside _og_meta, but section override is safer) ── --}}
@section('title')
    <title>{{ $viewModel->title }} | {{ $viewModel->siteName }}</title>
@endsection

{{-- ── Main content ──────────────────────────────────────────────────────── --}}
@section('content')

<style>
    /* ── Layout ─────────────────────────────────────── */
    .page { min-height: 100dvh; display: flex; flex-direction: column; }

    /* ── Top bar ─────────────────────────────────────── */
    .topbar {
        background: var(--red);
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .topbar__logo { height: 32px; width: auto; border-radius: 6px; }
    .topbar__name {
        color: #fff;
        font-weight: 700;
        font-size: .95rem;
        letter-spacing: .02em;
    }
    .topbar__badge {
        margin-left: auto;
        background: rgba(255,255,255,.18);
        color: #fff;
        font-size: .7rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    /* ── Product card ────────────────────────────────── */
    .card-wrap {
        flex: 1;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 24px 16px 40px;
    }
    .card {
        background: var(--card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        width: 100%;
        max-width: 480px;
    }

    /* ── Hero image ──────────────────────────────────── */
    .hero {
        position: relative;
        background: #f1f5f9;
        aspect-ratio: 4/3;
        overflow: hidden;
    }
    .hero__img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 16px;
    }
    .hero__store-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--red);
        color: #fff;
        font-size: .68rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: .07em;
    }

    /* ── Body ───────────────────────────────────────── */
    .body { padding: 20px 22px 24px; }

    /* Taxonomy pills */
    .pills { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
    .pill {
        font-size: .7rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .pill--cat  { background: #EFF6FF; color: #1D4ED8; }
    .pill--brand { background: #FEF9C3; color: #92400E; }

    /* Product name */
    .product-name {
        font-size: 1.2rem;
        font-weight: 800;
        line-height: 1.3;
        color: var(--text);
        margin-bottom: 10px;
    }

    /* Description */
    .product-desc {
        font-size: .875rem;
        color: var(--muted);
        line-height: 1.65;
        margin-bottom: 18px;
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Divider */
    .divider {
        height: 1px;
        background: var(--border);
        margin: 16px 0;
    }

    /* Price section */
    .price-block { margin-bottom: 20px; }
    .price-label {
        font-size: .72rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: 4px;
    }
    .price-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--red);
        line-height: 1.1;
    }
    .price-currency {
        font-size: .9rem;
        font-weight: 600;
        color: var(--muted);
        margin-left: 2px;
    }

    /* CTA buttons */
    .cta-group { display: flex; flex-direction: column; gap: 10px; }

    .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        padding: 13px 18px;
        font-size: .9rem;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: opacity .15s, transform .1s;
        text-align: center;
        width: 100%;
    }
    .btn:active { transform: scale(.98); opacity: .9; }

    .btn--primary {
        background: linear-gradient(135deg, #D32F2F, #B71C1C);
        color: #fff;
        box-shadow: 0 4px 14px rgba(211,47,47,.35);
    }
    .btn--ghost {
        background: var(--red-light);
        color: var(--red-dark);
    }
    .btn__icon { font-size: 1.1rem; }

    /* ── Footer ─────────────────────────────────────── */
    .footer {
        background: var(--text);
        color: rgba(255,255,255,.55);
        text-align: center;
        font-size: .75rem;
        padding: 14px 20px;
        line-height: 1.6;
    }
    .footer a { color: rgba(255,255,255,.8); font-weight: 600; }

    /* ── Responsive ─────────────────────────────────── */
    @media (min-width: 640px) {
        .card-wrap { padding: 40px 24px 60px; }
        .product-name { font-size: 1.4rem; }
        .price-value { font-size: 2rem; }
        .cta-group { flex-direction: row; }
        .btn { width: auto; flex: 1; }
    }
</style>

<div class="page">

    {{-- ── Top navigation bar ──────────────────────────────────────────────── --}}
    <header class="topbar" role="banner">
        <img
            class="topbar__logo"
            src="https://generaldrugcentre.com/logo/logo.png"
            alt="PS General Drugs Centre Logo"
            width="32"
            height="32"
        >
        <span class="topbar__name">PS General Drugs Centre</span>
        <span class="topbar__badge">
            {{ $viewModel->storeType === 'wholesales' ? 'Wholesales' : 'Retail' }}
        </span>
    </header>

    {{-- ── Product card ──────────────────────────────────────────────────── --}}
    <main class="card-wrap" role="main">
        <article class="card" itemscope itemtype="https://schema.org/Product">

            {{-- Hero image --}}
            <div class="hero">
                <img
                    class="hero__img"
                    src="{{ $viewModel->image }}"
                    alt="{{ $viewModel->title }}"
                    width="480"
                    height="360"
                    loading="eager"
                    fetchpriority="high"
                    itemprop="image"
                >
                <span class="hero__store-badge">
                    {{ $viewModel->storeType === 'wholesales' ? '🏭 Wholesales' : '🛍️ Retail' }}
                </span>
            </div>

            {{-- Body --}}
            <div class="body">

                {{-- Taxonomy pills --}}
                @if($viewModel->category || $viewModel->brand)
                <div class="pills">
                    @if($viewModel->category)
                        <span class="pill pill--cat" itemprop="category">
                            {{ $viewModel->category }}
                        </span>
                    @endif
                    @if($viewModel->brand)
                        <span class="pill pill--brand" itemprop="brand" itemscope itemtype="https://schema.org/Brand">
                            <span itemprop="name">{{ $viewModel->brand }}</span>
                        </span>
                    @endif
                </div>
                @endif

                {{-- Product name --}}
                <h1 class="product-name" itemprop="name">{{ $viewModel->title }}</h1>

                {{-- Description --}}
                @if($viewModel->description)
                <p class="product-desc" itemprop="description">
                    {{ $viewModel->description }}
                </p>
                @endif

                {{-- Price --}}
                @if($viewModel->formattedPrice())
                <div class="divider" aria-hidden="true"></div>

                <div class="price-block" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <meta itemprop="priceCurrency" content="{{ $viewModel->currency }}">
                    <meta itemprop="price" content="{{ number_format((float) $viewModel->price, 2, '.', '') }}">
                    <meta itemprop="availability" content="{{ $viewModel->schemaAvailability() }}">

                    <p class="price-label">{{ $viewModel->priceLabel }}</p>
                    <p class="price-value">
                        {{ $viewModel->formattedPrice() }}
                        <span class="price-currency">{{ $viewModel->currency }}</span>
                    </p>
                </div>

                <div class="divider" aria-hidden="true"></div>
                @endif

                {{-- CTA Buttons --}}
                <div class="cta-group">
                    <a
                        href="https://generaldrugcentre.com/app"
                        class="btn btn--primary"
                        id="btn-download-app"
                        aria-label="Download the PS GDC app to order {{ $viewModel->title }}"
                    >
                        <span class="btn__icon" aria-hidden="true">📱</span>
                        Order on the App
                    </a>

                    <a
                        href="https://generaldrugcentre.com"
                        class="btn btn--ghost"
                        id="btn-visit-store"
                        aria-label="Visit PS General Drugs Centre website"
                    >
                        <span class="btn__icon" aria-hidden="true">🏪</span>
                        Visit Store
                    </a>
                </div>

            </div>{{-- /.body --}}
        </article>
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────────────── --}}
    <footer class="footer" role="contentinfo">
        <p>
            Shared via <a href="https://generaldrugcentre.com" rel="noopener">PS General Drugs Centre</a>
            &nbsp;·&nbsp; Your #1 Online Drugs &amp; Supermarket in Nigeria
        </p>
    </footer>

</div>{{-- /.page --}}
@endsection

{{-- ── JSON-LD ─────────────────────────────────────────────────────────────── --}}
@section('jsonld')
    @include('share._json_ld')
@endsection
