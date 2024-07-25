<?php

namespace App\Imports;

use App\Models\Stock;
use App\Models\StockRestriction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockRestrictionImport implements ToCollection, WithHeadingRow
{
    private array $config;
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $data = [];
        $localID = $collection->map(function ($row){
            if(isset($row['local_stock_id'])) {
                return $row['local_stock_id'];
            }
            return false;
        })->toArray();

        $stocks = Stock::whereIn('local_stock_id', $localID)->get();

        foreach ($stocks as $stock)
        {
            $data[] = [
                'stock_id' => $stock->id,
                'group_id' => $this->config['group_id'],
                'group_type' => $this->config['group_type'],
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        StockRestriction::insert($data);
    }
}
