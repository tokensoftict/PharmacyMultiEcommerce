<?php

namespace App\Services\Stock;

use App\Classes\ApplicationEnvironment;
use App\Models\Stock;

class StockSeoService
{
    /**
     * Generate a URL-safe SEO slug from a product name and its ID.
     *
     * Example:
     *   "Paracetamol 500mg Tablet (Pack of 12)" + id 42
     *   → "paracetamol-500mg-tablet-pack-of-12-42"
     *
     * @param string   $name  The product name.
     * @param int|null $id    The stock ID to append for uniqueness.
     * @return string
     */
    public static function generateSlug(string $name, int $id = null): string
    {
        // 1. Lowercase
        $slug = mb_strtolower($name);

        // 2. Replace any character that is NOT alphanumeric / space with a space
        $slug = preg_replace('/[^a-z0-9\s]/', ' ', $slug);

        // 3. Collapse whitespace into a single hyphen
        $slug = preg_replace('/\s+/', '-', trim($slug));

        // 4. Strip leading/trailing hyphens
        $slug = trim($slug, '-');

        // 5. Collapse consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // 6. Append the stock ID for guaranteed uniqueness
        if ($id !== null) {
            $slug = $slug . '-' . $id;
        }

        return $slug;
    }

    /**
     * Generate and persist the SEO slug directly on a Stock model.
     * The stock must already be saved (needs a valid ID).
     *
     * @param Stock $stock
     * @return void
     */
    public static function applyToStock(Stock $stock): void
    {
        if (!$stock->name || !$stock->id) {
            return;
        }

        $slug = self::generateSlug($stock->name, $stock->id);

        // Use updateQuietly so we don't trigger further model events
        $stock->updateQuietly(['seo' => $slug]);
    }

    /**
     * Build the complete, public SEO URL for a product.
     *
     * Uses the current ApplicationEnvironment store type to pick the
     * correct route prefix (wholesales or retail), matching the share
     * routes defined in routes/share.php:
     *   /{storeType}/p/{slug}
     *
     * Example:
     *   https://domain.com/wholesales/p/paracetamol-500mg-tablet-42
     *
     * @param  string|null $slug  The product's SEO slug (from the `seo` column).
     * @return string|null        Full URL, or null when no slug is available.
     */
    public static function generateUrl(?string $slug): ?string
    {
        if (!$slug) {
            return null;
        }

        $storeType = ApplicationEnvironment::$stock_model_string === 'supermarkets_stock_prices'
            ? 'retail'
            : 'wholesales';

        return url("{$storeType}/p/{$slug}");
    }
}
