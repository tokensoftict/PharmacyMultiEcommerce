<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Services\Stock\StockSeoService;
use Illuminate\Console\Command;

class GenerateProductSeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-product-seo
                            {--force : Regenerate SEO for ALL products, even those that already have one}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SEO slugs for all products. By default only fills products where seo IS NULL. Use --force to regenerate all.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = $this->option('force');

        $query = Stock::withoutGlobalScopes()
            ->whereNotNull('name');

        if (!$force) {
            $query->whereNull('seo');
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info($force
                ? 'No products with a name found.'
                : 'All products already have an SEO slug. Use --force to regenerate.'
            );
            return self::SUCCESS;
        }

        $this->info($force
            ? "Regenerating SEO for all {$total} products..."
            : "Generating SEO for {$total} products missing a slug..."
        );

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;

        $query->chunkById(100, function ($stocks) use ($bar, &$updated) {
            foreach ($stocks as $stock) {
                $slug = StockSeoService::generateSlug($stock->name, $stock->id);

                // updateQuietly skips model events to prevent recursive loops
                $stock->updateQuietly(['seo' => $slug]);

                $updated++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Done! SEO slugs generated/updated for {$updated} product(s).");

        return self::SUCCESS;
    }
}
