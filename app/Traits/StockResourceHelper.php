<?php

namespace App\Traits;

use App\Classes\ApplicationEnvironment;

trait StockResourceHelper
{
    /**
     * Get the current department based on the application environment.
     *
     * @return string
     */
    protected function getDepartment(): string
    {
        return ApplicationEnvironment::$stock_model_string === 'supermarkets_stock_prices' ? 'retail' : 'wholesales';
    }

    /**
     * Filter custom prices for the current department.
     *
     * @param mixed $stock
     * @return array
     */
    protected function filterCustomPrices($stock): array
    {
        $department = $this->getDepartment();
        
        return ($stock->stockquantityprices ?? collect())
            ->where('department', $department)
            ->map(function ($item) {
                return [
                    'price' => $item->price,
                    'min_qty' => $item->min_qty,
                    'max_qty' => $item->max_qty,
                    'price_formatted' => number_format($item->price, 2),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Filter and format stock options for the current department.
     *
     * @param mixed $stock
     * @return array
     */
    protected function filterStockOptions($stock): array
    {
        $department = $this->getDepartment();
        $statusField = ($department === 'retail') ? 'retail_status' : 'wholesales_status';
        $priceField = ($department === 'retail') ? 'retail_price' : 'wholesales_price';
        $prefixField = ($department === 'retail') ? 'retail_price_prefix' : 'wholesales_price_prefix';

        return ($stock->stock_option_values ?? collect())
            ->map(function ($optionValue) use ($statusField, $priceField, $prefixField) {
                $filteredOptions = collect($optionValue->options)
                    ->filter(function ($opt) use ($statusField) {
                        return (isset($opt[$statusField]) && $opt[$statusField] == 1);
                    })
                    ->map(function ($opt) use ($priceField, $prefixField) {
                        return [
                            'id' => $opt['id'] ?? null,
                            'name' => $opt['name'] ?? '',
                            'price' => $opt[$priceField] ?? 0,
                            'price_prefix' => $opt[$prefixField] ?? '+',
                        ];
                    })
                    ->values();

                if ($filteredOptions->isEmpty()) {
                    return null;
                }

                return [
                    'option_name' => $optionValue->option_name,
                    'option_type' => $optionValue->option_type,
                    'options' => $filteredOptions->toArray(),
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Get dependent products for the stock.
     *
     * @param mixed $stock
     * @return array
     */
    protected function getDependentProducts($stock): array
    {
        if ($this->getDepartment() !== 'wholesales') {
            return [];
        }

        return ($stock->dependent_products ?? collect())
            ->map(function ($item) {
                $dependentStock = $item->dependent_stock;
                
                if (!$dependentStock) {
                    return [
                        'stock_id' => $item->dependent_local_stock_id,
                        'parent' => $item->parent,
                        'child' => $item->child,
                        'not_found' => true
                    ];
                }

                $priceModel = ApplicationEnvironment::$stock_model_string;
                $price = $dependentStock->{$priceModel}->price ?? 0;

                return [
                    'id' => $dependentStock->id,
                    'stock_id' => $item->dependent_local_stock_id,
                    'name' => $dependentStock->name,
                    'price' => $price,
                    'price_formatted' => money($price),
                    'image' => $dependentStock->product_image,
                    'parent' => $item->parent ?? 1,
                    'child' => $item->child ?? 1,
                ];
            })
            ->toArray();
    }

    /**
     * Resolve selected option IDs into detailed objects.
     *
     * @param mixed $stock
     * @param array $selectedIds
     * @return array
     */
    protected function resolveSelectedOptions($stock, array $selectedIds): array
    {
        $department = $this->getDepartment();
        $priceField = ($department === 'retail') ? 'retail_price' : 'wholesales_price';
        $prefixField = ($department === 'retail') ? 'retail_price_prefix' : 'wholesales_price_prefix';

        $resolved = [];
        foreach (($stock->stock_option_values ?? collect()) as $optionValue) {
            foreach ($optionValue->options as $opt) {
                if (in_array($opt['id'] ?? null, $selectedIds)) {
                    $resolved[] = [
                        'id' => $opt['id'],
                        'name' => $opt['name'] ?? '',
                        'price' => (float)($opt[$priceField] ?? 0),
                        'price_prefix' => $opt[$prefixField] ?? '+',
                        'group_name' => $optionValue->option_name,
                    ];
                }
            }
        }

        return $resolved;
    }
}
