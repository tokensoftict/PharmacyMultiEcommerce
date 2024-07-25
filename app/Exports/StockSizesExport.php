<?php

namespace App\Exports;

use App\Stock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockSizesExport implements FromCollection, WithHeadings
{

    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'ID',
            'Stock Name',
            //'Quantity',
            'Category',
            'Size',
        ];
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return DB::table('stocks')->select(
            'stocks.id as ID',
            'stocks.name as stock_name',
            //'stocks.quantity',
            'productcategories.name as category_name',
            'stock_sizes.product_size'
        )->leftJoin('productcategories','stocks.productcategory_id','=','productcategories.id')
         ->leftJoin('stock_sizes','stocks.id','=','stock_sizes.stock_id')
            ->get();
    }
}
