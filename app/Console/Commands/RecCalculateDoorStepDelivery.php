<?php

namespace App\Console\Commands;

use App\Models\DeliveryTownDistance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RecCalculateDoorStepDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rec-calculate-door-step-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DeliveryTownDistance::query()
            ->where("delivery_type", "1")
            ->chunk(100, function ($deliveryTownDistances) {
                foreach ($deliveryTownDistances as $deliveryTownDistance) {
                    $from = Carbon::parse($deliveryTownDistance->starting_date);
                    $to = now();

                    $days = $from->diffInDays($to, true);
                    dump($days);
                    if ($days <= $deliveryTownDistance->reset_time_days) {

                        $frequency = Str::plural($deliveryTownDistance->interval_frequency);

                        // Add interval using Carbon
                        $newDate = $from->copy()->add(
                            $frequency,
                            $deliveryTownDistance->interval_no
                        );
                        // Update and save
                        $deliveryTownDistance->starting_date = $newDate;
                        $deliveryTownDistance->save();
                    }
                }
            });
    }
}
