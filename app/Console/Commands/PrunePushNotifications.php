<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PrunePushNotifications extends Command
{
    protected $signature = 'push-notifications:prune
                            {--days=21 : Number of days of push notification data to keep}
                            {--chunk=1000 : Number of push notifications to delete per batch}
                            {--dry-run : Only show what would be deleted}';

    protected $description = 'Delete push notifications (and their customers/stocks rows) older than the retention period (default 21 days / 3 weeks)';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $chunk = max(1, (int) $this->option('chunk'));

        if ($days < 1) {
            $this->error('--days must be at least 1.');
            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);

        $oldNotifications = fn() => DB::table('push_notifications')->where('created_at', '<', $cutoff);

        $total = $oldNotifications()->count();

        if ($this->option('dry-run')) {
            $customers = DB::table('push_notification_customers')
                ->whereIn('push_notification_id', $oldNotifications()->select('id'))
                ->count();
            $stocks = DB::table('push_notification_stocks')
                ->whereIn('push_notification_id', $oldNotifications()->select('id'))
                ->count();

            $this->info("Dry run: would delete {$total} push notifications, {$customers} customer rows and {$stocks} stock rows created before {$cutoff}.");
            return self::SUCCESS;
        }

        $deletedNotifications = $deletedCustomers = $deletedStocks = 0;

        // Delete in batches (children first) to keep transactions and locks small
        while (($ids = $oldNotifications()->orderBy('id')->limit($chunk)->pluck('id'))->isNotEmpty()) {
            DB::transaction(function () use ($ids, &$deletedNotifications, &$deletedCustomers, &$deletedStocks) {
                $deletedCustomers += DB::table('push_notification_customers')->whereIn('push_notification_id', $ids)->delete();
                $deletedStocks += DB::table('push_notification_stocks')->whereIn('push_notification_id', $ids)->delete();
                $deletedNotifications += DB::table('push_notifications')->whereIn('id', $ids)->delete();
            });
        }

        $this->info("Deleted {$deletedNotifications} push notifications, {$deletedCustomers} customer rows and {$deletedStocks} stock rows created before {$cutoff}.");

        return self::SUCCESS;
    }
}
