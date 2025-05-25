<?php
namespace App\Traits;

use App\Classes\Settings;
use App\Models\NewStockArrival;
use App\Models\Pricechangehistory;
use App\Models\Stock;
use App\Services\Utilities\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait ModelFilterTraits
{

    /**
     * @param $query
     * @param array $filter
     * @return mixed
     */
    public function scopefilterdata($query, array $filter)
    {
        foreach ($filter as $key => $value) {
            if(str_contains($key ,'between'))
            {
                $key = str_replace('between.','', $key);

                $query->whereBetween($this->getQueryTable($key, true).$key,$value );
            }
            else {
                $query->where($this->getQueryTable($key).$key, $value);
            }
        }

        return $query;
    }


    /**
     * @param $criterial
     * @param $between
     * @return string
     */
    protected function getQueryTable($criterial, $between = false) : string
    {
        if(str_contains($criterial, "."))  return $between === false ? $criterial : "";

        return $this->table.".";
    }


    /**
     * @return void
     */
    public final function triggerNewArrivalCount() : void
    {
        $settings = Settings::getSetting();
        $newArrivalCountTrigger = $settings->get("new_arrival_count_trigger_".Str::snake($this->app->name));

        if(is_null($newArrivalCountTrigger)){
            $newArrivalCountTrigger = 5; // set it to constant
            $settings->put("new_arrival_count_trigger".Str::snake($this->app->name), $newArrivalCountTrigger);
        }

        if($newArrivalCountTrigger > 0){
            $newArrivalCount = $settings->get("arrivalCount".Str::snake($this->app->name));
            if(is_null($newArrivalCount)){
                $settings->put("arrivalCount".Str::snake($this->app->name), 0);
            }
        }


        $arrivalCount = $settings->get("arrivalCount".Str::snake($this->app->name));
        $arrivalCount++;
        $settings->put("arrivalCount".Str::snake($this->app->name), $arrivalCount);


        if($arrivalCount == $newArrivalCountTrigger or $arrivalCount > $newArrivalCountTrigger){
            $settings->put("arrivalCount".Str::snake($this->app->name), 0);
            $product = NewStockArrival::query()->orderBy("id", "DESC")->first()?->stock?->name;
            $newlyArrivedStocks = NewStockArrival::query()->select("stock_id")->orderBy('id','DESC')->limit($newArrivalCountTrigger)->pluck('stock_id');
            //create notification tobe pushed

            $pushNotification = new PushNotificationService();
            $pushNotification->createNotification(
                [
                    "title" => "New Stock Arrival Alert!",
                    "body" => $product." and ".( $newArrivalCountTrigger - 1)." more products are available, Shop Now",
                    "payload"=>json_encode($newlyArrivedStocks),
                    "device_ids"=>[],
                    "app_id"=>$this->app_id,
                    "type"=>"topic",
                    "action"=>"stocks",
                    "status" =>"DRAFT",
                ]
            );
            $pushNotification->approve()->send(); // push the push notification to the device

        }



    }

}
