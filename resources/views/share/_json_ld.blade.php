{{--
    ── Schema.org Product JSON-LD ────────────────────────────────────────────
    Provides structured data for Google Shopping, search snippets, and
    the Google Rich Results Test. Embedded at end of <body>.
--}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": {{ Js::from($viewModel->title) }},
    "description": {{ Js::from($viewModel->description) }},
    "image": {{ Js::from($viewModel->image) }},
    "sku": {{ Js::from((string) $viewModel->localStockId) }},
    "url": {{ Js::from($viewModel->url) }},
    @if($viewModel->brand)
    "brand": {
        "@type": "Brand",
        "name": {{ Js::from($viewModel->brand) }}
    },
    @endif
    @if($viewModel->category)
    "category": {{ Js::from($viewModel->category) }},
    @endif
    "offers": {
        "@type": "Offer",
        "priceCurrency": {{ Js::from($viewModel->currency) }},
        @if($viewModel->price !== null)
        "price": {{ number_format($viewModel->price, 2, '.', '') }},
        @else
        "price": "0.00",
        @endif
        "availability": {{ Js::from($viewModel->schemaAvailability()) }},
        "url": {{ Js::from($viewModel->url) }},
        "seller": {
            "@type": "Organization",
            "name": {{ Js::from($viewModel->siteName) }},
            "url": "https://generaldrugcentre.com"
        }
    },
    "provider": {
        "@type": "Organization",
        "name": {{ Js::from($viewModel->siteName) }},
        "url": "https://generaldrugcentre.com",
        "logo": "https://generaldrugcentre.com/logo/logo.png"
    }
}
</script>
