<?php

namespace App\Imports;

use App\Models\Old\Stock;
use App\Models\StockSize;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockSizeImport implements ToCollection,WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        ini_set('memory_limit', '1256M');

        foreach ($collection as $row)
        {
            if(empty($row['id']) && empty($row['size'])) continue;
            if($row['size'] == "1") continue; /// 1 is the default for all stock
            $stockExist = Stock::find($row['id']);
            if(!$stockExist) continue;
            StockSize::updateOrCreate(
                [
                    'stock_id'=>$row['id']
                ],
                [
                    'stock_id'=>$row['id'],
                    'product_size'=>$row['size']
                ]
            );
        }
    }
}
