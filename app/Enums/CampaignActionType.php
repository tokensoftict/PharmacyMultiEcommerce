<?php

namespace App\Enums;

class CampaignActionType
{
    const NONE          = 'none';
    const OPEN_PRODUCT  = 'open_product';
    const OPEN_CATEGORY = 'open_category';
    const OPEN_CART     = 'open_cart';
    const OPEN_CHECKOUT = 'open_checkout';
    const OPEN_ORDER    = 'open_order';
    const OPEN_STORE    = 'open_store';
    const OPEN_URL      = 'open_url';
    const OPEN_DEEP_LINK = 'open_deep_link';
    const APPLY_COUPON  = 'apply_coupon';

    public static function all(): array
    {
        return [
            self::NONE, self::OPEN_PRODUCT, self::OPEN_CATEGORY,
            self::OPEN_CART, self::OPEN_CHECKOUT, self::OPEN_ORDER,
            self::OPEN_STORE, self::OPEN_URL, self::OPEN_DEEP_LINK, self::APPLY_COUPON,
        ];
    }
}
