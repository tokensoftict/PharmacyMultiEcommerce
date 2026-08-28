<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Http\Controllers\ApiController;
use App\Services\Campaign\CartAbandonmentService;
use App\Classes\ApplicationEnvironment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CartAbandonmentHookController
 *
 * Called internally after any cart mutation to update the abandonment tracker.
 * This is NOT a public endpoint — it is called as a service from cart controllers.
 * If you prefer, you can use this as a trait instead.
 */
class CartAbandonmentHookController extends ApiController
{
    public static function touchCart(Request $request, array $cartItems, float $total): void
    {
        $user = $request->user();
        if (!$user) return;

        $storeType = ApplicationEnvironment::$stock_model_string === 'wholessales_stock_prices'
            ? 'wholesale'
            : 'retail';

        app(CartAbandonmentService::class)->touchCart($user, $storeType, $cartItems, $total);
    }

    public static function onOrderPlaced(int $userId, string $storeType): void
    {
        app(CartAbandonmentService::class)->onOrderPlaced($userId, $storeType);
    }

    public static function onCartCleared(Request $request): void
    {
        $user = $request->user();
        if (!$user) return;

        $storeType = ApplicationEnvironment::$stock_model_string === 'wholessales_stock_prices'
            ? 'wholesale'
            : 'retail';

        app(CartAbandonmentService::class)->onCartCleared($user->id, $storeType);
    }
}
