<?php

namespace App\Console\Commands;

use App\Services\Campaign\CartAbandonmentService;
use App\Services\Campaign\CampaignConditionsService;
use App\Services\Campaign\CampaignEligibilityService;
use App\Services\Campaign\CampaignPushService;
use Illuminate\Console\Command;

/**
 * CheckCartAbandonmentCommand
 *
 * Scans the cart_abandonment_trackers table for abandoned carts
 * and dispatches push notifications for matching campaigns.
 *
 * Scheduled to run every 15 minutes via the scheduler.
 *
 * Usage: php artisan campaigns:check-cart-abandonment
 */
class CheckCartAbandonmentCommand extends Command
{
    protected $signature   = 'campaigns:check-cart-abandonment
                             {--threshold=60 : Minutes of inactivity before a cart is considered abandoned}';
    protected $description = 'Check for abandoned carts and dispatch campaign push notifications';

    public function handle(CampaignPushService $pushService): int
    {
        $threshold = (int) $this->option('threshold');

        $this->info("Scanning for carts abandoned for {$threshold}+ minutes...");

        $abandonmentService = new CartAbandonmentService($threshold);
        $eligibilityService = new CampaignEligibilityService(new CampaignConditionsService());

        $dispatched = $abandonmentService->scanAndDispatch($eligibilityService, $pushService);

        $this->info("Done. Dispatched abandonment notifications for {$dispatched} carts.");

        return Command::SUCCESS;
    }
}
