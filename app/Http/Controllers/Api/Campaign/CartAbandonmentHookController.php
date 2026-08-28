<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Http\Controllers\ApiController;
use App\Services\Campaign\CartAbandonmentService;
use App\Classes\ApplicationEnvironment;
use Illuminate\Http\Request;

/**
 * CartAbandonmentHookController
 *
 * Called internally after any cart mutation to update the abandonment tracker.
 */
class CartAbandonmentHookController extends ApiController
{
    /**
     * Touch cart using application model (computes exact shopping cart total & items).
     */
    public static function touchCartFromApplication($application): void
    {
        if (!$application) return;

        $storeType = ApplicationEnvironment::$stock_model_string === 'wholessales_stock_prices'
            ? 'wholesale'
            : 'retail';

        $cartData = $application->getShoppingCartItems();
        $total = (float) ($cartData['meta']['totalItemsInCarts'] ?? 0);
        $rawCart = array_values((array) ($application->cart ?? []));

        app(CartAbandonmentService::class)->touchCart($application, $storeType, $rawCart, $total);
    }

    public static function touchCart(Request $request, array $cartItems, float $total): void
    {
        $user = $request->user() ?? getApplicationModel();
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
        $user = $request->user() ?? getApplicationModel();
        if (!$user) return;

        $storeType = ApplicationEnvironment::$stock_model_string === 'wholessales_stock_prices'
            ? 'wholesale'
            : 'retail';

        app(CartAbandonmentService::class)->onCartCleared($user->id, $storeType);
    }
}
