<?php

namespace App\Services\Campaign;

use App\Enums\CampaignEvent;
use App\Models\CartAbandonmentTracker;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * CartAbandonmentService
 *
 * Determines when a cart has been abandoned and dispatches the CART_ABANDONED
 * campaign trigger. "Abandonment" is defined as:
 *   - Cart has ≥1 item, and
 *   - No cart update for N minutes (configurable; default 60), and
 *   - No order placed since last cart update.
 *
 * Called by:
 *   1. The AddItemToCartController hook (to update the tracker)
 *   2. The ConfirmOrderController hook (to reset tracker on order placement)
 *   3. ClearAllItemsInCartController hook (to reset on cart clear)
 *   4. Scheduled command (CheckCartAbandonmentCommand) for the periodic scan.
 */
class CartAbandonmentService
{
    /**
     * Default: how many minutes of inactivity counts as abandonment.
     */
    private int $abandonmentThresholdMinutes;

    public function __construct(int $abandonmentThresholdMinutes = 60)
    {
        $this->abandonmentThresholdMinutes = $abandonmentThresholdMinutes;
    }

    /**
     * Update the tracker whenever a cart item is added, updated, or removed.
     */
    public function touchCart(User $user, string $storeType, array $cartItems, float $total): void
    {
        CartAbandonmentTracker::recordCartState($user->id, $storeType, $cartItems, $total);
    }

    /**
     * Mark the tracker as order-placed (clears abandonment state).
     */
    public function onOrderPlaced(int $userId, string $storeType): void
    {
        CartAbandonmentTracker::markOrderPlaced($userId, $storeType);
    }

    /**
     * Mark the tracker as cart-cleared.
     */
    public function onCartCleared(int $userId, string $storeType): void
    {
        CartAbandonmentTracker::markCartCleared($userId, $storeType);
    }

    /**
     * Scan for abandoned carts and dispatch campaign triggers.
     * Called by the scheduled command.
     *
     * @return int Number of abandonment triggers dispatched.
     */
    public function scanAndDispatch(CampaignEligibilityService $eligibility, CampaignPushService $pushService): int
    {
        $cutoff = now()->subMinutes($this->abandonmentThresholdMinutes);
        $dispatched = 0;

        $trackers = CartAbandonmentTracker::query()
            ->where('item_count', '>', 0)
            ->where('abandonment_triggered', false)
            ->where('last_activity_at', '<=', $cutoff)
            ->whereNull('order_placed_at')
            ->with('user')
            ->get();

        foreach ($trackers as $tracker) {
            $user = $tracker->user;
            if (!$user) continue;

            $storeType = $tracker->store_type; // retail | wholesale

            $context = [
                'cart_total'      => $tracker->cart_total,
                'cart_item_count' => $tracker->item_count,
                'store_type'      => $storeType,
                'cart_snapshot'   => $tracker->cart_snapshot ?? [],
            ];

            try {
                // Resolve eligible CART_ABANDONED campaigns
                $campaigns = $eligibility->resolveEligible(
                    $user,
                    CampaignEvent::CART_ABANDONED,
                    $storeType,
                    $context
                );

                foreach ($campaigns as $campaign) {
                    if (in_array($campaign->delivery_channel, ['push', 'both'])) {
                        $pushService->sendToUser($campaign, $user);
                    }
                }

                // Mark triggered regardless — even if no campaigns matched,
                // we reset so the next modification restarts the cycle.
                $tracker->update([
                    'abandonment_triggered'      => true,
                    'last_notified_at'           => now(),
                    'abandon_notification_count' => $tracker->abandon_notification_count + 1,
                ]);

                if ($campaigns->isNotEmpty()) {
                    $dispatched++;
                }

            } catch (\Throwable $e) {
                Log::error("[CartAbandonmentService] Error for tracker #{$tracker->id}: {$e->getMessage()}");
            }
        }

        return $dispatched;
    }
}
