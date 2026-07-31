<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" prefix="og: https://ogp.me/ns#">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    {{-- ── SEO / OG / Twitter meta ──────────────────────────────────── --}}
    @yield('meta')

    {{-- ── Page title ───────────────────────────────────────────────── --}}
    @yield('title')

    {{-- ── Preconnects for faster font load ───────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="https://generaldrugcentre.com/logo/logo.png">

    {{-- ── Critical inline CSS ─────────────────────────────────────────
         No external CSS files → zero render-blocking resources.
         Crawlers and real users both get first-paint immediately.
    ──────────────────────────────────────────────────────────────────── --}}
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:        #D32F2F;
            --red-light:  #FFEBEE;
            --red-dark:   #B71C1C;
            --blue:       #1565C0;
            --gold:       #F9A825;
            --text:       #1A1D1E;
            --muted:      #64748B;
            --border:     #E2E8F0;
            --bg:         #F8FAFC;
            --card:       #FFFFFF;
            --radius:     14px;
            --shadow:     0 4px 24px rgba(0,0,0,.08);
        }

        html { font-size: 16px; -webkit-text-size-adjust: 100%; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100dvh;
        }

        img { display: block; max-width: 100%; height: auto; }

        a { color: inherit; text-decoration: none; }

        .sr-only {
            position: absolute; width: 1px; height: 1px;
            padding: 0; margin: -1px; overflow: hidden;
            clip: rect(0,0,0,0); white-space: nowrap; border: 0;
        }
    </style>
</head>

<body>
    {{-- ── Page content ────────────────────────────────────────────────── --}}
    @yield('content')

    {{-- ── Schema.org JSON-LD ─────────────────────────────────────────── --}}
    @yield('jsonld')

    {{-- No JavaScript needed — share pages are purely static HTML --}}
</body>

</html>
