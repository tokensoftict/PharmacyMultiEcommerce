<?php


namespace App\Repositories;



use App\Classes\Settings;
use App\Models\Address;
use App\Models\DeliveryMethod;
use App\Models\DeliveryTownDistance;
use App\Models\Stock;
use App\Models\WholesalesUser;

class DsdRepository
{


    /**
     * @param array|null $shoppingCart
     * @param DeliveryMethod $methodOfDelivery
     * @param array|null $extraData
     * @return array
     */
    public final function calculateDeliveryTotal(?array $shoppingCart, DeliveryMethod $methodOfDelivery , ?array $extraData) : array
    {

        $applicationUser = getApplicationModel();

        if(!$applicationUser) return [
            'status' => false,
            'name'=>$methodOfDelivery->name,
            'amount'=>0,
            'error' => ["Application user error, Please restart the application to complete your checkout"]
        ];

        if(!$applicationUser instanceof WholesalesUser)  return [
            'status' => false,
            'name'=>$methodOfDelivery->name,
            'amount'=>0,
            'error' => ['Door Step Delivery does not exist for Supermarket!']
        ];

        $address = $applicationUser->address_id;

        if(isset($applicationUser->checkout['deliveryAddressId'])){
            $address = $applicationUser->checkout['deliveryAddressId'];
        }

        $address = Address::with(['town','state'])->find($address ?? 0);

        if(!$address){
            return [
                'status' => false,
                'name'=>$methodOfDelivery->name,
                'amount'=>0,
                'error' => ['Does step Delivery is not available for your delivery address']
            ];
        }


        if(!isset($address->town_id) and !is_null($address->town_id)) {
            return [
                'status' => false,
                'name'=>$methodOfDelivery->name,
                'amount'=>0,
                'error' => ['Your Town is not supported for Door step Delivery!']
            ];
        }

        $door_step_down_distance = DeliveryTownDistance::where('town_id',$address->town_id)->first();
        if(!$door_step_down_distance){
            return [
                'status' => false,
                'name'=>$methodOfDelivery->name,
                'amount'=>0,
                'error' => ['Your Town is not supported for Door step Delivery!']
            ];
        }

        $nextDayDelivery = $this->getNextDelivery($door_step_down_distance);
        if(!$nextDayDelivery) {
            return [
                'status' => false,
                'name'=>$methodOfDelivery->name,
                'amount'=>0,
                'error' => ['Unable to calculate the date of your delivery']
            ];
        }

        $doorStepDeliveryAmount = Stock::whereIn("id", array_keys($shoppingCart))->get()->sum(function($stock) use ($shoppingCart){
            return $stock->doorstep * $shoppingCart[$stock->id]['quantity'];
        });

        if($doorStepDeliveryAmount <= $door_step_down_distance->minimum_shipping_amount)
        {
            $doorStepDeliveryAmount = $door_step_down_distance->fixed_shipping_amount;
        }

        $nextDayDelivery = carbonize($nextDayDelivery)->format('D, jS, F Y');
        return [
            'status' => true,
            'name'=>$methodOfDelivery->name." (".$nextDayDelivery.")",
            'amount'=>$doorStepDeliveryAmount,
            'deliveryDate' => $nextDayDelivery,
            'amount_formatted'=>money($doorStepDeliveryAmount),
        ];
    }

    /**
     * @param DeliveryTownDistance $door_step_down_distance
     * @return string
     */
    public function getNextDelivery(DeliveryTownDistance $door_step_down_distance) : string
    {
        if(!isset($door_step_down_distance->delivery_days)) return false;

        $days = $door_step_down_distance->delivery_days;
        if(!is_array($days)) $days = [$days];

        $add = $door_step_down_distance->no." ".$door_step_down_distance->frequency;

        $next48hours = strtotime($add);

        $next_delivery_days = [];

        foreach ($days as $day)
        {
            $next_delivery_days[] = strtotime("next $day",$next48hours);
        }

        $next_date = min($next_delivery_days);

        if(!$next_date) return false;

        return date('Y-m-d',$next_date);

    }

    /**
     * @param array|null $shoppingCart
     * @param DeliveryMethod $methodOfDelivery
     * @param array|null $extraData
     * @return array
     */
    public final function getDsdAnalysis(?array $shoppingCart, DeliveryMethod $methodOfDelivery , ?array $extraData) : array
    {
        $applicationUser = getApplicationModel();

        if(!$applicationUser) return [
            'status' => false,
            'name'=>$methodOfDelivery->name,
            'amount'=>0,
            'error' => ["Application user error, Please restart the application to complete your checkout"]
        ];

        if(!$applicationUser instanceof WholesalesUser)  return [
            'status' => false,
            'name'=>$methodOfDelivery->name,
            'amount'=>0,
            'error' => ['Door Step Delivery does not exist for Supermarket!']
        ];

        $address = $applicationUser->address_id;

        if(isset($applicationUser->checkout['deliveryAddressId'])){
            $address = $applicationUser->checkout['deliveryAddressId'];
        }

        $address = Address::with(['town','state'])->find($address ?? 0);

        if(!$address){
            return [
                'status' => false,
                'name'=>$methodOfDelivery->name,
                'amount'=>0,
                'error' => ['Does step Delivery is not available for your delivery address']
            ];
        }


        if(!isset($address->town_id) and !is_null($address->town_id)) {
            return [
                'status' => false,
                'name'=>$methodOfDelivery->name,
                'amount'=>0,
                'error' => ['Your Town is not supported for Door step Delivery!']
            ];
        }

        $door_step_down_distance = DeliveryTownDistance::where('town_id',$address->town_id)->first();

        $analysis = [];
        $doorStepDeliveryAmount = Stock::whereIn("id", array_keys($shoppingCart))->get()->sum(function($stock) use ($shoppingCart, &$analysis){
            $analysis[] = [
                "name" => $stock->name,
                "quantity" =>$shoppingCart[$stock->id]['quantity'],
                "rate" => money($stock->doorstep),
                "total" => money($stock->doorstep * $shoppingCart[$stock->id]['quantity']),
            ];
            return $stock->doorstep * $shoppingCart[$stock->id]['quantity'];
        });

        if($doorStepDeliveryAmount <= $door_step_down_distance->minimum_shipping_amount)
        {
            $doorStepDeliveryAmount = $door_step_down_distance->fixed_shipping_amount;
        }

        return [
            "status" => true,
            "total"=> money($doorStepDeliveryAmount),
            "delivery_date"=>carbonize($this->getNextDelivery($door_step_down_distance))->format('D, jS, F Y'),
        ];
    }


}
