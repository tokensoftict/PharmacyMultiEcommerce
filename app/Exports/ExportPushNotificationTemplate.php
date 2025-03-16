<?php

namespace App\Exports;

use App\Classes\ApplicationEnvironment;
use App\Models\PushNotificationStock;
use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportPushNotificationTemplate implements FromCollection, WithHeadings
{
    public ?int $id;
    public function __construct(int $id = NULL)
    {
        $this->id = $id;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->id != NULL){

            return PushNotificationStock::query()->select(
                "stocks.id",
                "stocks.name",
                "manufacturers.name as manufacturer",
                "productcategories.name as productcategory",
                "classifications.name as classification",
                "stocks.box",
                ApplicationEnvironment::$stock_model_string.".price",
                ApplicationEnvironment::$stock_model_string.".quantity",
            )->join('stocks', "stocks.id", "=", "promotion_items.stock_id")
                ->join(ApplicationEnvironment::$stock_model_string, 'stocks.id', '=', ApplicationEnvironment::$stock_model_string.'.stock_id' )
                ->leftjoin("manufacturers", "stocks.manufacturer_id", "manufacturers.id")
                ->leftjoin("productcategories", "stocks.productcategory_id", "productcategories.id")
                ->leftjoin("classifications", "stocks.classification_id", "classifications.id")
                ->where("push_notification_stocks.push_notification_id", $this->id)
                ->where("promotion_items.app_id", ApplicationEnvironment::$model_id)
                ->get();

        } else {
            return Stock::query()
                ->select("stocks.id",
                    "stocks.name",
                    "manufacturers.name as manufacturer",
                    "productcategories.name as productcategory",
                    "classifications.name as classification",
                    "stocks.box",
                    ApplicationEnvironment::$stock_model_string.".price",
                    ApplicationEnvironment::$stock_model_string.".quantity"
                )
                ->join(ApplicationEnvironment::$stock_model_string, 'stocks.id', '=', ApplicationEnvironment::$stock_model_string.'.stock_id' )
                ->leftjoin("manufacturers", "stocks.manufacturer_id", "manufacturers.id")
                ->leftjoin("productcategories", "stocks.productcategory_id", "productcategories.id")
                ->leftjoin("classifications", "stocks.classification_id", "classifications.id")
                ->get();

        }
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
        ];
    }
}
