<?php

namespace App\Http\Resources\Api\Stock;

use App\Classes\ApplicationEnvironment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description ?? 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet, animi architecto asperiores assumenda aut blanditiis consequatur ducimus, enim eum ipsam quaerat quasi quia ratione saepe tempora ullam veritatis, vero voluptates? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet, animi architecto asperiores assumenda aut blanditiis consequatur ducimus, enim eum ipsam quaerat quasi quia ratione saepe tempora ullam veritatis, vero voluptates? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet, animi architecto asperiores assumenda aut blanditiis consequatur ducimus, enim eum ipsam quaerat quasi quia ratione saepe tempora ullam veritatis, vero voluptates? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet, animi architecto asperiores assumenda aut blanditiis consequatur ducimus, enim eum ipsam quaerat quasi quia ratione saepe tempora ullam veritatis, vero voluptates?',
            "price" => number_format($this->{ApplicationEnvironment::$stock_model_string}->price),
            "price_not_formatted" => $this->{ApplicationEnvironment::$stock_model_string}->price,
            "productcategory" => $this->productcategory?->name,
            "manufacturer" => $this->manufacturer?->name,
            "classification" => $this->classification?->name,
            "productgroup" => $this->productgroup?->name,
            "image" => $this->product_image,
            "max" => $this->max,
            "box" => $this->box,
            "sachet" => $this->sachet,
            "carton" => $this->carton,
            "special" => number_format(rand(3000, 5000),2),
            "special_not_formatted" =>  1,
            "quantity" => $this->{ApplicationEnvironment::$stock_model_string}->quantity,
            "expiry_date" => $this?->{ApplicationEnvironment::$stock_model_string}?->expiry_date?->format("F jS, Y"),
            "doorstep" => number_format(rand(100.5, 200.5),2),
            "store"=> $this->{ApplicationEnvironment::$stock_model_string}
        ];
    }
}

