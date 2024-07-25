<?php


namespace App\Repositories;



use App\Classes\Settings;
use App\Models\Address;

class DsdRepository
{

    private $usermodel;

    private $auth;

    private $user_table;


    public function __construct()
    {
        $this->usermodel = config()->get('USERMODEL');

        $this->auth = config()->get('auth_');

        $this->user_table = config()->get('user_table');

    }


    public function checkIFDSDSupported($delivery, $deliverydata)
    {
        $check = $this->calculate_delivery_code($delivery, $deliverydata);
        if($check['amount'] == 0) return false;
        return true;
    }


    public function add($request,$shipping){
        $shipping->template_settings_value = json_encode($request->data);
        $shipping->update();
        return $shipping;
    }


    public function calculate_delivery_code($delivery, $deliverydata){

        $tarrif = app(Settings::class)->delivery_tariff;

        if(!isset($delivery['delivery_address']))
        {
            return [
                'name'=>$deliverydata->name,
                'non_format_amount' => 0,
                'amount'=>0
            ];
        }

        $address = Address::with(['town','state'])->find($delivery['delivery_address']);

        if($address->town_id == NULL)
        {
            return [
                'name'=>$deliverydata->name,
                'non_format_amount' => 0,
                'amount'=>0
            ];
        }

        $door_step_down_distance = DoorStepDeliveryTownDistance::where('town_id',$address->town_id)->first();

        $next_delivery_date = $this->getNextDelivery($door_step_down_distance);

        if(!$next_delivery_date)
        {
            return [
                'name'=>$deliverydata->name,
                'non_format_amount' => 0,
                'amount'=>0
            ];
        }

        $user_id = auth(config()->get('auth_'))->id();

        $model = config()->get('USERMODEL');

        $user =  $model::find($user_id);

        if(empty($user->cart)){
            return [
                'name'=>$deliverydata->name,
                'non_format_amount' => 0,
                'amount'=>0
            ];
        }else{
            $carts = json_decode($user->cart, true);
        }

        $stock_ids = array_keys($carts);

        $stocks = Stock::with('ProductSize')->whereIn('id',$stock_ids)->get();

        $total = 0;

        foreach ($stocks as $stock)
        {
            $size = 1;

            if(!empty($stock->productSize->product_size))
            {
                $size = $stock->productSize->product_size;
            }

            $total+=(($size * $door_step_down_distance->town_distance) * $tarrif) * $carts[$stock->id]['qty'];
        }

        if($total <= $door_step_down_distance->minimum_shipping_amount)
        {
            $total = $door_step_down_distance->fixed_shipping_amount;
        }

        $next_delivery_date = convert_date($next_delivery_date);

        return [
            'name'=>$deliverydata->name."(".$address->town->town_name.")"." -  Next Delivery Date : ".$next_delivery_date,
            'amount'=>cur_format($total),
            'non_format_amount' => $total,
            'next_delivery_date' => $next_delivery_date,
            'address' => $address->address_1.", ".$address->zone->name.", ".$address->city.", ".$address->town->town_name,
            "note" => "Note : Minimum Delivery Amount for ".$address->town->town_name." is ₦ ".cur_format($door_step_down_distance->fixed_shipping_amount)
        ];

    }


    public function checkouttemplate($payment){
        return '';
    }

    public function checkouttemplateMobile($payment){
        return "";
    }

    private function convertfrequency()
    {

    }

    public function getNextDelivery($door_step_down_distance)
    {
        if(!isset($door_step_down_distance->delivery_days)) return false;

        $days = $door_step_down_distance->delivery_days;

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


    public function get_dynamic_data($checkoutData,$deli)
    {
        return  $this->calculate_delivery_code($checkoutData,$deli);

    }


}
