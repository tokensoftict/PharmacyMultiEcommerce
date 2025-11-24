<?php

namespace App\Exports;

use App\Classes\ApplicationEnvironment;
use App\Models\PromotionItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportPromotionTemplate implements FromCollection, WithHeadings
{

    public ?int $id;
    public function __construct(int $id = NULL)
    {
        $this->id = $id;
    }


    public function collection()
    {
        if($this->id != NULL){

            return PromotionItem::query()->select(
                "stocks.id",
                "stocks.name",
                "manufacturers.name as manufacturer",
                "productcategories.name as productcategory",
                "classifications.name as classification",
                "stocks.box",
                ApplicationEnvironment::$stock_model_string.".price",
                ApplicationEnvironment::$stock_model_string.".quantity",
                "promotion_items.price as promotion_price",
            )->join('stocks', "stocks.id", "=", "promotion_items.stock_id")
                ->join(ApplicationEnvironment::$stock_model_string, 'stocks.id', '=', ApplicationEnvironment::$stock_model_string.'.stock_id' )
                ->leftjoin("manufacturers", "stocks.manufacturer_id", "manufacturers.id")
                ->leftjoin("productcategories", "stocks.productcategory_id", "productcategories.id")
                ->leftjoin("classifications", "stocks.classification_id", "classifications.id")
                ->where("promotion_items.promotion_id", $this->id)
                ->where("promotion_items.app_id", ApplicationEnvironment::$model_id)
                ->get();
        }
        $me  = Stock::query()
            ->select("stocks.id",
                "stocks.name",
                "manufacturers.name as manufacturer",
                "productcategories.name as productcategory",
                "classifications.name as classification",
                "stocks.box",
                ApplicationEnvironment::$stock_model_string.".price",
                ApplicationEnvironment::$stock_model_string.".quantity",
                DB::raw("0 as promotion_price"),
            )
            ->join(ApplicationEnvironment::$stock_model_string, 'stocks.id', '=', ApplicationEnvironment::$stock_model_string.'.stock_id' )
            ->leftjoin("manufacturers", "stocks.manufacturer_id", "manufacturers.id")
            ->leftjoin("productcategories", "stocks.productcategory_id", "productcategories.id")
            ->leftjoin("classifications", "stocks.classification_id", "classifications.id")
            ->first();

        dd($me);
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
            'Price',
            'Quantity',
            'Promo Price'
        ];
    }
}
