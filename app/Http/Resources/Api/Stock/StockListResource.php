<?php

namespace App\Http\Resources\Api\Stock;

use App\Classes\ApplicationEnvironment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockListResource extends JsonResource
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
            "price" => number_format($this->{ApplicationEnvironment::$stock_model_string}->price, 2),
            "price_not_formatted" => $this->{ApplicationEnvironment::$stock_model_string}->price,
            "image" => $this->product_image,
            "quantity" => $this->{ApplicationEnvironment::$stock_model_string}->quantity,
            "max" => $this->max,
            "box" => $this->box,
            "sachet" => $this->sachet,
            "carton" => $this->carton,
            "special" => number_format(rand(3000, 5000),2),
            "special_not_formatted" =>  1,
            "doorstep" => number_format(rand(100.5, 200.5),2),
            "expiry_date" => $this?->{ApplicationEnvironment::$stock_model_string}?->expiry_date?->format("F jS, Y"),
        ];
    }
}
