<?php

namespace App\Enums;

class CampaignEvent
{
    // App lifecycle
    const APP_OPEN        = 'APP_OPEN';
    const APP_FOREGROUND  = 'APP_FOREGROUND';
    const SESSION_STARTED = 'SESSION_STARTED';

    // Auth
    const LOGIN  = 'LOGIN';
    const SIGNUP = 'SIGNUP';

    // Navigation
    const HOME_OPENED = 'HOME_OPENED';

    // Product
    const PRODUCT_VIEWED  = 'PRODUCT_VIEWED';
    const CATEGORY_VIEWED = 'CATEGORY_VIEWED';
    const SEARCH_PERFORMED = 'SEARCH_PERFORMED';

    // Cart
    const ADD_TO_CART     = 'ADD_TO_CART';
    const REMOVE_FROM_CART = 'REMOVE_FROM_CART';
    const CART_UPDATED    = 'CART_UPDATED';
    const CART_ABANDONED  = 'CART_ABANDONED';

    // Checkout
    const CHECKOUT_STARTED = 'CHECKOUT_STARTED';

    // Order
    const ORDER_PLACED    = 'ORDER_PLACED';
    const ORDER_CANCELLED = 'ORDER_CANCELLED';
    const ORDER_COMPLETED = 'ORDER_COMPLETED';

    // Payment
    const PAYMENT_STARTED  = 'PAYMENT_STARTED';
    const PAYMENT_SUCCESS  = 'PAYMENT_SUCCESS';
    const PAYMENT_FAILED   = 'PAYMENT_FAILED';

    // Wishlist
    const ADD_TO_WISHLIST = 'ADD_TO_WISHLIST';

    // Stock & promos
    const STOCK_CHANGED      = 'STOCK_CHANGED';
    const PRICE_CHANGED      = 'PRICE_CHANGED';
    const PROMOTION_EXPIRING = 'PROMOTION_EXPIRING';

    // Special
    const RANDOM = 'RANDOM';
    const CUSTOM = 'CUSTOM';

    public static function all(): array
    {
        return [
            self::APP_OPEN, self::APP_FOREGROUND, self::SESSION_STARTED,
            self::LOGIN, self::SIGNUP,
            self::HOME_OPENED,
            self::PRODUCT_VIEWED, self::CATEGORY_VIEWED, self::SEARCH_PERFORMED,
            self::ADD_TO_CART, self::REMOVE_FROM_CART, self::CART_UPDATED, self::CART_ABANDONED,
            self::CHECKOUT_STARTED,
            self::ORDER_PLACED, self::ORDER_CANCELLED, self::ORDER_COMPLETED,
            self::PAYMENT_STARTED, self::PAYMENT_SUCCESS, self::PAYMENT_FAILED,
            self::ADD_TO_WISHLIST,
            self::STOCK_CHANGED, self::PRICE_CHANGED, self::PROMOTION_EXPIRING,
            self::RANDOM, self::CUSTOM,
        ];
    }
}
