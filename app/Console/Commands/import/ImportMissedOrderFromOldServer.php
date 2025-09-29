<?php

namespace App\Console\Commands\import;

use App\Models\Old\Order;
use App\Services\ImportOrderService;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;

class ImportMissedOrderFromOldServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-missed-order-from-old-server';

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

        $invoiceNumber  = text(
            label : "Invoice Number or Order ID",
            default: "",
            required: true
        );

        if($invoiceNumber === "") {
            return Command::SUCCESS;
        }

        $order = Order::with(['user','address','address.zone','orderStatus','orderTotalOrders','orderProducts','paymentMethod','shippingMethod','shippingAddress','shippingAddress.zone'])
            ->where(function($query) use ($invoiceNumber){
                $query->orWhere('id', $invoiceNumber)->orWhere('invoice_no', $invoiceNumber);
            })->first();
        if($order) {
            $importOrderService->handle($order->toArray(), true);
        }
    }
}
