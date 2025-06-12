<?php

namespace App\Console\Commands\import;

use App\Models\Old\Order;
use App\Services\ImportOrderService;
use Illuminate\Console\Command;

class ImportOrderFromOldServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-order-from-old-server';

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
        $importOrderService = app(ImportOrderService::class);
        Order::with(['user','address','address.zone','orderStatus','orderTotalOrders','orderProducts','paymentMethod','shippingMethod','shippingAddress','shippingAddress.zone'])
            ->where('id', '>',7) // dont include test orders they have zero local id
            ->chunk(10, function ($orders) use ($importOrderService) {
                foreach ($orders as $order) {
                    $importOrderService->handle($order->toArray());
                }
            });
    }
}
