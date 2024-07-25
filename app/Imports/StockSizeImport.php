<?php

namespace App\Imports;

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
        foreach ($collection as $row)
        {
            if(empty($row['id']) && empty($row['size'])) continue;

            StockSize::updateOrCreate(
                ['stock_id'=>$row['id']],
                [
                    'stock_id'=>$row['id'],
                    'size'=>$row['size']
                ]
            );
        }
    }
}
