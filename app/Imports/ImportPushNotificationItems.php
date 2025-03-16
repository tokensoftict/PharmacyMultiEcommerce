<?php

namespace App\Imports;

use App\Models\PushNotification;
use App\Models\PushNotificationStock;
use App\Models\SupermarketsStockPrice;
use App\Models\WholessalesStockPrice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportPushNotificationItems implements ToCollection,WithHeadingRow
{

    private array $pushNotificationItems = [];
    private PushNotification $pushNotification;

    public function __construct(PushNotification $pushNotification)
    {
        $this->pushNotification = $pushNotification;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $app = $this->pushNotification->app;
        $stock_model = match ($app->model_id){
            6 => SupermarketsStockPrice::class,
            default=>WholessalesStockPrice::class,
        };

        foreach ($collection as $row){
            $row = $row->toArray();

            $checkStock = $stock_model::where('stock_id', $row['id'])->first();
            if(!$checkStock) continue;

            $this->pushNotificationItems[] = new PushNotificationStock([
                'push_notification_id' => $this->pushNotification->id,
                'stock_id' => $row['id'],
                'app_id' => $this->pushNotification->app_id,
            ]);

        }
    }

    /**
     * @return array
     */
    public function getPushNotificationItems() : array
    {
        return $this->pushNotificationItems;

    }
}
