<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CartAbandonmentTracker
 *
 * @property int $id
 * @property int $user_id
 * @property string $store_type
 * @property int $item_count
 * @property float $cart_total
 * @property array|null $cart_snapshot
 * @property array|null $stock_levels_snapshot
 * @property Carbon|null $last_activity_at
 * @property Carbon|null $last_notified_at
 * @property Carbon|null $order_placed_at
 * @property bool $abandonment_triggered
 * @property int $abandon_notification_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 */
class CartAbandonmentTracker extends Model
{
    protected $table = 'cart_abandonment_trackers';

    protected $casts = [
        'cart_snapshot'          => 'array',
        'stock_levels_snapshot'  => 'array',
        'last_activity_at'       => 'datetime',
        'last_notified_at'       => 'datetime',
        'order_placed_at'        => 'datetime',
        'abandonment_triggered'  => 'bool',
        'item_count'             => 'int',
        'cart_total'             => 'float',
        'abandon_notification_count' => 'int',
    ];

    protected $fillable = [
        'user_id', 'store_type', 'item_count', 'cart_total',
        'cart_snapshot', 'stock_levels_snapshot',
        'last_activity_at', 'last_notified_at', 'order_placed_at',
        'abandonment_triggered', 'abandon_notification_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Upsert tracker on every cart modification.
     */
    public static function recordCartState(int $userId, string $storeType, array $cartItems, float $total): self
    {
        $snapshot = array_map(fn($item) => [
            'id'       => $item['id'],
            'quantity' => $item['quantity'] ?? 1,
        ], $cartItems);

        $tracker = self::firstOrNew(['user_id' => $userId, 'store_type' => $storeType]);
        $tracker->fill([
            'item_count'             => count($cartItems),
            'cart_total'             => $total,
            'cart_snapshot'          => array_values($snapshot),
            'last_activity_at'       => now(),
            'abandonment_triggered'  => false,
            'order_placed_at'        => null,
        ]);
        $tracker->save();

        return $tracker;
    }

    /**
     * Mark the cart as having an order placed, resetting abandonment state.
     */
    public static function markOrderPlaced(int $userId, string $storeType): void
    {
        self::where('user_id', $userId)
            ->where('store_type', $storeType)
            ->update([
                'abandonment_triggered'      => false,
                'abandon_notification_count' => 0,
                'order_placed_at'            => now(),
                'item_count'                 => 0,
                'cart_total'                 => 0,
                'cart_snapshot'              => null,
            ]);
    }

    /**
     * Mark the cart as having an active empty cart (cleared by user).
     */
    public static function markCartCleared(int $userId, string $storeType): void
    {
        self::where('user_id', $userId)
            ->where('store_type', $storeType)
            ->update([
                'abandonment_triggered'      => false,
                'abandon_notification_count' => 0,
                'item_count'                 => 0,
                'cart_total'                 => 0,
                'cart_snapshot'              => null,
                'last_activity_at'           => now(),
            ]);
    }
}
