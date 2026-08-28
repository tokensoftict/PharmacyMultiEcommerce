<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" prefix="og: https://ogp.me/ns#">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    {{-- ── Open Graph & Social Preview Meta ─────────────────────────────── --}}
    @include('referral._og_meta')

    {{-- ── Preconnects for font ─────────────────────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ $viewModel->faviconUrl() }}">
    <link rel="shortcut icon" href="{{ $viewModel->faviconUrl() }}">
    <link rel="apple-touch-icon" href="{{ $viewModel->faviconUrl() }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red: #D32F2F;
            --red-dark: #B71C1C;
            --red-light: #FFEBEE;
            --green: #2E7D32;
            --text: #0F172A;
            --muted: #64748B;
            --border: #E2E8F0;
            --bg: #F8FAFC;
            --card: #FFFFFF;
            --radius: 16px;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        html { font-size: 16px; -webkit-text-size-adjust: 100%; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ────────────────────────────────────────────── */
        .header {
            background: linear-gradient(135deg, var(--red), var(--red-dark));
            color: #fff;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(211, 47, 47, 0.2);
        }

        .header__brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header__logo {
            height: 38px;
            width: auto;
            border-radius: 8px;
            background: #fff;
            padding: 2px;
        }

        .header__title {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .header__tag {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── Main Container ────────────────────────────────────── */
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px 48px;
        }

        .card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            width: 100%;
            max-width: 520px;
            overflow: hidden;
            text-align: center;
        }

        /* ── Hero Banner ───────────────────────────────────────── */
        .hero {
            background: linear-gradient(135deg, #1E293B, #0F172A);
            color: #fff;
            padding: 36px 24px 30px;
            position: relative;
        }

        .hero__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(211, 47, 47, 0.25);
            border: 1px solid rgba(211, 47, 47, 0.5);
            color: #FF8A80;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 16px;
        }

        .hero__title {
            font-size: 1.45rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .hero__highlight {
            color: #FF5252;
        }

        .hero__sub {
            font-size: 0.92rem;
            color: #94A3B8;
            max-width: 420px;
            margin: 0 auto;
            line-height: 1.5;
        }

        /* ── Content Body ──────────────────────────────────────── */
        .body {
            padding: 28px 24px 32px;
        }

        /* ── Code Box ──────────────────────────────────────────── */
        .code-section {
            background: #F1F5F9;
            border: 2px dashed #CBD5E1;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .code-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .code-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .code-text {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--red-dark);
            letter-spacing: 0.1em;
            font-family: monospace, sans-serif;
        }

        .copy-btn {
            background: #fff;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .copy-btn:hover {
            background: var(--red);
            color: #fff;
            border-color: var(--red);
        }

        /* ── Features List ─────────────────────────────────────── */
        .features {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 28px;
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #FAFAFA;
            border: 1px solid #F1F5F9;
            padding: 12px 14px;
            border-radius: 10px;
        }

        .feature-icon {
            font-size: 1.25rem;
            line-height: 1;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .feature-text {
            font-size: 0.86rem;
            color: #334155;
            line-height: 1.45;
        }

        .feature-text strong {
            color: var(--text);
        }

        /* ── CTA Buttons ───────────────────────────────────────── */
        .cta-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--red), var(--red-dark));
            color: #fff;
            padding: 15px 24px;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(211, 47, 47, 0.35);
            transition: transform 0.15s, box-shadow 0.15s;
            margin-bottom: 16px;
        }

        .cta-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(211, 47, 47, 0.45);
        }

        .cta-primary:active {
            transform: translateY(1px);
        }

        /* ── Store App Badges ──────────────────────────────────── */
        .store-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .store-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #0F172A;
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            transition: opacity 0.15s;
        }

        .store-btn:hover { opacity: 0.9; }

        .store-btn svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
            flex-shrink: 0;
        }

        /* ── Redirect Notice ───────────────────────────────────── */
        .redirect-notice {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 12px;
        }

        .redirect-notice a {
            color: var(--red);
            font-weight: 600;
            text-decoration: underline;
        }

        /* ── Footer ────────────────────────────────────────────── */
        .footer {
            background: #0F172A;
            color: #94A3B8;
            text-align: center;
            font-size: 0.78rem;
            padding: 18px 20px;
            line-height: 1.6;
        }

        .footer a {
            color: #E2E8F0;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .store-buttons { flex-direction: column; }
            .hero__title { font-size: 1.3rem; }
        }
    </style>
</head>

<body>

    {{-- ── Top Navigation Bar ────────────────────────────────────────── --}}
    <header class="header">
        <div class="header__brand">
            <img class="header__logo" src="https://generaldrugcentre.com/logo/logo.png" alt="PS GDC Logo" width="38" height="38">
            <span class="header__title">PS General Drugs Centre</span>
        </div>
        <span class="header__tag">Referral Invite</span>
    </header>

    {{-- ── Main Card ─────────────────────────────────────────────────── --}}
    <main class="main">
        <div class="card">

            {{-- Hero --}}
            <div class="hero">
                <div class="hero__badge">
                    <span>🎁 Special Invitation</span>
                </div>
                <h1 class="hero__title">
                    You've Been Invited by <br>
                    <span class="hero__highlight">{{ $viewModel->referrerName }}</span>!
                </h1>
                <p class="hero__sub">
                    Download the PS General Drug Centre mobile app, create your account, and start earning loyalty reward points today!
                </p>
            </div>

            {{-- Card Body --}}
            <div class="body">

                {{-- Referral Code Box --}}
                @if(!empty($viewModel->referralCode))
                <div class="code-section">
                    <span class="code-label">Your Referral Promo Code</span>
                    <div class="code-row">
                        <span class="code-text" id="referral-code">{{ $viewModel->referralCode }}</span>
                        <button class="copy-btn" onclick="copyReferralCode()" id="copy-btn" type="button" aria-label="Copy referral code">
                            📋 <span id="copy-text">Copy</span>
                        </button>
                    </div>
                </div>
                @endif

                {{-- Feature Benefits --}}
                <div class="features">
                    <div class="feature-item">
                        <span class="feature-icon">💊</span>
                        <div class="feature-text">
                            <strong>100% Genuine Pharmaceuticals</strong><br>
                            Top-grade healthcare, cosmetics, and supermarket products at wholesale &amp; retail prices.
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">⚡</span>
                        <div class="feature-text">
                            <strong>Fast &amp; Reliable Delivery</strong><br>
                            Quick order processing and doorstep delivery anywhere in Nigeria.
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🎁</span>
                        <div class="feature-text">
                            <strong>Earn Loyalty Points</strong><br>
                            Receive bonus loyalty points when you register and verify your phone number.
                        </div>
                    </div>
                </div>

                {{-- Primary Detour Download Action --}}
                <a href="{{ $viewModel->detourUrl }}" class="cta-primary" id="cta-install-btn">
                    🚀 Open / Install App &amp; Claim Bonus
                </a>

                {{-- Direct Store Buttons --}}
                <div class="store-buttons">
                    <a href="{{ $viewModel->playStoreUrl }}" class="store-btn" target="_blank" rel="noopener noreferrer" id="btn-playstore">
                        <svg viewBox="0 0 24 24">
                            <path d="M3.609 1.814L13.792 12 3.61 22.186c-.365-.365-.61-.926-.61-1.606V3.42c0-.68.245-1.241.61-1.606zM15.207 13.414l2.457-2.457-12.78-7.38 10.323 9.837zm0-2.828L4.884 20.423l12.78-7.38-2.457-2.457zm1.414 1.414l3.195 1.845c.783.452.783 1.189 0 1.641l-3.195 1.845-2.043-2.043 2.043-2.043z"/>
                        </svg>
                        Google Play
                    </a>
                    <a href="{{ $viewModel->appStoreUrl }}" class="store-btn" target="_blank" rel="noopener noreferrer" id="btn-appstore">
                        <svg viewBox="0 0 24 24">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.38c.62-.75 1.04-1.8 1.01-2.85-.92.04-2.03.62-2.69 1.38-.58.67-1.1 1.74-1.01 2.78 1.03.08 2.07-.56 2.69-1.31z"/>
                        </svg>
                        App Store
                    </a>
                </div>

                <p class="redirect-notice">
                    Redirecting you to the app in <span id="countdown">2</span>s...
                    <br>
                    <a href="{{ $viewModel->detourUrl }}" id="manual-link">Click here if not redirected automatically.</a>
                </p>

            </div>{{-- /.body --}}
        </div>
    </main>

    {{-- ── Footer ─────────────────────────────────────────────────────── --}}
    <footer class="footer">
        <p>
            &copy; {{ date('Y') }} <a href="https://generaldrugcentre.com">PS General Drugs Centre</a>. All rights reserved.
            <br>
            Your #1 Online Pharmacy &amp; Supermarket in Nigeria.
        </p>
    </footer>

    <script>
        function copyReferralCode() {
            var codeEl = document.getElementById('referral-code');
            if (!codeEl) return;
            var code = codeEl.innerText.trim();
            navigator.clipboard.writeText(code).then(function() {
                var copyText = document.getElementById('copy-text');
                if (copyText) {
                    copyText.innerText = 'Copied! ✓';
                    setTimeout(function() {
                        copyText.innerText = 'Copy';
                    }, 2000);
                }
            }).catch(function() {
                alert('Referral code: ' + code);
            });
        }

        // Auto redirect for mobile browser visitors
        var detourUrl = @json($viewModel->detourUrl);
        var count = 2;
        var countdownEl = document.getElementById('countdown');

        // Only auto-redirect if on a mobile device or if user agent is not a crawler
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        var isCrawler = /facebookexternalhit|whatsapp|telegrambot|twitterbot|slackbot|discordbot|applebot/i.test(navigator.userAgent);

        if (isMobile && !isCrawler && detourUrl) {
            var interval = setInterval(function() {
                count--;
                if (countdownEl) countdownEl.innerText = count;
                if (count <= 0) {
                    clearInterval(interval);
                    window.location.href = detourUrl;
                }
            }, 1000);
        } else {
            var notice = document.querySelector('.redirect-notice');
            if (notice && !isMobile) {
                notice.innerHTML = '<a href="' + detourUrl + '">Click above to open or download the app.</a>';
            }
        }
    </script>
</body>
</html>
