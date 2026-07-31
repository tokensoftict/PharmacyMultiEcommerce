<?php

namespace App\Http\ViewModels;

use App\Models\Stock;

/**
 * Read-only DTO that carries all values the product share Blade view needs.
 * Resolves every field eagerly so the template never triggers lazy queries.
 */
readonly class ProductShareViewModel
{
    public string $title;
    public string $description;
    public string $image;
    public string $url;
    public string $canonicalUrl;
    public string|null $brand;
    public string|null $category;
    public float|null $price;
    public string $currency;
    public string $siteName;
    public string $locale;
    public string $storeType;   // 'retail' or 'wholesales'
    public string $priceLabel;  // human-friendly label
    public int $localStockId;

    public function __construct(Stock $stock, string $storeType, string $currentUrl)
    {
        $defaultDescription = config('app.name') . ' — Your #1 Online Drugs & Supermarket';

        // ── Core fields ─────────────────────────────────────────────────────
        $this->title       = $stock->name ?? 'Product';
        $this->description = $stock->description
            ? strip_tags((string) $stock->description)
            : $defaultDescription;

        // ── Image ────────────────────────────────────────────────────────────
        // Prefer the Spatie media-library full URL; fall back to logo placeholder.
        $mediaUrl = $stock->stock_media?->media?->getFullUrl();
        $this->image = $mediaUrl
            ? (str_starts_with($mediaUrl, 'http') ? $mediaUrl : asset($mediaUrl))
            : asset('logo/no-image.png');

        // ── URLs ─────────────────────────────────────────────────────────────
        $this->url          = $currentUrl;
        $this->canonicalUrl = $currentUrl;

        // ── Taxonomy ─────────────────────────────────────────────────────────
        $this->brand    = $stock->manufacturer?->name;
        $this->category = $stock->productcategory?->name;

        // ── Price ─────────────────────────────────────────────────────────────
        $this->storeType = $storeType;

        if ($storeType === 'wholesales') {
            $this->price      = $stock->wholessales_stock_prices?->price;
            $this->priceLabel = 'Wholesales Price';
        } else {
            $this->price      = $stock->supermarkets_stock_prices?->price;
            $this->priceLabel = 'Retail Price';
        }

        $this->currency = 'NGN';

        // ── Misc ──────────────────────────────────────────────────────────────
        $this->siteName     = 'PS General Drugs Centre';
        $this->locale       = 'en_NG';
        $this->localStockId = (int) $stock->local_stock_id;
    }

    /**
     * Returns the OG description truncated to 200 chars (safe for all platforms).
     */
    public function ogDescription(): string
    {
        return mb_strimwidth($this->description, 0, 200, '…');
    }

    /**
     * Returns the URL to use as the page favicon.
     *
     * Priority:
     *   1. Product image (when the product has a real image uploaded)
     *   2. Store logo — /logo/logo.png (fallback when no product image)
     *
     * We detect "no image" by checking whether the URL points to the
     * placeholder filename set in StockModelTrait / StockSeoService.
     */
    public function faviconUrl(): string
    {
        if (str_contains($this->image, 'no-image')) {
            return asset('logo/logo.png');
        }

        return $this->image;
    }

    /**
     * Returns the formatted price string, e.g. "₦1,500.00".
     */
    public function formattedPrice(): string|null
    {
        if ($this->price === null) {
            return null;
        }

        return '₦' . number_format($this->price, 2);
    }

    /**
     * Returns Schema.org availability string.
     */
    public function schemaAvailability(): string
    {
        return 'https://schema.org/InStock';
    }
}
