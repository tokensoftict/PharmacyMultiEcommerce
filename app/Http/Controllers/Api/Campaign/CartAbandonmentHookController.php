<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Services\Campaign\CartAbandonmentService;
use App\Classes\ApplicationEnvironment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * CartAbandonmentHookController
 *
 * Called internally after any cart mutation to update the abandonment tracker.
 */
class CartAbandonmentHookController extends ApiController
{
    /**
     * Resolve the real `users.id`. The cart owner passed around is often an
     * application profile (SupermarketUser / WholesalesUser) whose own `id`
     * differs from users.id, which breaks the FK on cart_abandonment_trackers.
     */
    private static function resolveUserId($model = null): ?int
    {
        $authUser = request()->user();
        if ($authUser instanceof User) {
            return (int) $authUser->id;
        }

        if ($model instanceof User) {
            return (int) $model->id;
        }

        if ($model && !empty($model->user_id)) {
            return (int) $model->user_id;
        }

        return null;
    }

    private static function storeType(): string
    {
        return ApplicationEnvironment::$stock_model_string === 'wholessales_stock_prices'
            ? 'wholesale'
            : 'retail';
    }

    /**
     * Touch cart using application model (computes exact shopping cart total & items).
     */
    public static function touchCartFromApplication($application): void
    {
        if (!$application) return;

        try {
            $userId = self::resolveUserId($application);
            if (!$userId) return;

            $cartData = $application->getShoppingCartItems();
            $total = (float) ($cartData['meta']['totalItemsInCarts'] ?? 0);
            $rawCart = array_values((array) ($application->cart ?? []));

            app(CartAbandonmentService::class)->touchCart($userId, self::storeType(), $rawCart, $total);
        } catch (\Throwable $e) {
            Log::warning('[CartAbandonment] touchCartFromApplication failed: ' . $e->getMessage());
        }
    }

    public static function touchCart(Request $request, array $cartItems, float $total): void
    {
        try {
            $userId = self::resolveUserId(getApplicationModel() ?: null);
            if (!$userId) return;

            app(CartAbandonmentService::class)->touchCart($userId, self::storeType(), $cartItems, $total);
        } catch (\Throwable $e) {
            Log::warning('[CartAbandonment] touchCart failed: ' . $e->getMessage());
        }
    }

    public static function onOrderPlaced(int $userId, string $storeType): void
    {
        try {
            app(CartAbandonmentService::class)->onOrderPlaced($userId, $storeType);
        } catch (\Throwable $e) {
            Log::warning('[CartAbandonment] onOrderPlaced failed: ' . $e->getMessage());
        }
    }

    public static function onCartCleared(Request $request): void
    {
        try {
            $userId = self::resolveUserId(getApplicationModel() ?: null);
            if (!$userId) return;

            app(CartAbandonmentService::class)->onCartCleared($userId, self::storeType());
        } catch (\Throwable $e) {
            Log::warning('[CartAbandonment] onCartCleared failed: ' . $e->getMessage());
        }
    }
}
