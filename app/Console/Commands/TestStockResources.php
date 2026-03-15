<?php

namespace App\Console\Commands;

use App\Classes\ApplicationEnvironment;
use App\Http\Resources\Api\Stock\StockListResource;
use App\Http\Resources\Api\Stock\StockShowResource;
use App\Models\Stock;
use Illuminate\Console\Command;

class TestStockResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-stock-resources {local_stock_id=6}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test stock resources filtering for retail and wholesales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $app = \App\Models\App::first();
        if ($app) {
           ApplicationEnvironment::createApplicationEnvironment($app);
        }

        $localStockId = $this->argument('local_stock_id');
        $stock = Stock::where('local_stock_id', $localStockId)->first();

        if (!$stock) {
            $this->error("Stock with local_stock_id $localStockId not found.");
            return;
        }

        $this->info("Testing Stock: " . $stock->name);

        // Simulate Retail Environment
        $this->info("\n--- RETAIL ENVIRONMENT ---");
        ApplicationEnvironment::$stock_model_string = 'supermarkets_stock_prices';
        
        $resource = new StockShowResource($stock);
        $data = $resource->toArray(request());
        
        $this->info("Custom Prices (Retail): " . json_encode($data['custom_price'], JSON_PRETTY_PRINT));
        $this->info("Stock Options (Retail): " . json_encode($data['stock_options'], JSON_PRETTY_PRINT));

        // Simulate Wholesales Environment
        $this->info("\n--- WHOLESALES ENVIRONMENT ---");
        ApplicationEnvironment::$stock_model_string = 'wholessales_stock_prices';
        
        $resource = new StockShowResource($stock);
        $data = $resource->toArray(request());
        
        $this->info("Custom Prices (Wholesales): " . json_encode($data['custom_price'], JSON_PRETTY_PRINT));
        $this->info("Stock Options (Wholesales): " . json_encode($data['stock_options'], JSON_PRETTY_PRINT));
        
        if (isset($data['dependent_products'])) {
            $this->info("Dependent Products: " . json_encode($data['dependent_products'], JSON_PRETTY_PRINT));
        }
    }
}
