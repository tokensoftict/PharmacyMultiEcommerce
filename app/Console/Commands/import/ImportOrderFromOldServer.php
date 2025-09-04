<?php

namespace App\Console\Commands\import;

use App\Models\Old\Order;
use App\Services\ImportOrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
        $lastOrder = Cache::get('last_order_id', 26279);
        $orders = Order::with(['user','address','address.zone','orderStatus','orderTotalOrders','orderProducts','paymentMethod','shippingMethod','shippingAddress','shippingAddress.zone'])
            ->where('id', '>',$lastOrder)->limit(10)->get();

        foreach ($orders as $order) {
            $importOrderService->handle($order->toArray());
        }

        $lastOrder = $orders->last();
        if($lastOrder){
            Cache::forget('last_order_id');
            Cache::set('last_order_id', $lastOrder->id);
        }
    }
}
