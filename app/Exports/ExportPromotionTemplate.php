<?php

namespace App\Exports;

use App\Models\Stock;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportPromotionTemplate implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Stock::query()
            ->select("stocks.id", "stocks.name", "manufacturers.name", "productcategories.name", "classifications.name", "box", "whole_price", "wholesales")
            ->join("manufacturers", "stocks.manufacturer_id", "manufacturers.id")
            ->join("productcategories", "stocks.productcategory_id", "productcategories.id")
            ->join("classifications", "stocks.classification_id", "classifications.id")
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Manufacturer',
            'Product Category',
            'Classification',
            'Box',
            'Quantity',
            'Price',
            'Promo Price'
        ];
    }
}
