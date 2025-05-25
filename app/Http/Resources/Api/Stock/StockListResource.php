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
            "price" => money($this->{ApplicationEnvironment::$stock_model_string}?->price),
            "price_not_formatted" => $this->{ApplicationEnvironment::$stock_model_string}?->price,
            "image" => $this->product_image,
            "quantity" => $this->{ApplicationEnvironment::$stock_model_string}?->quantity,
            "max" => $this->max,
            "box" => $this->box,
            "sachet" => $this->sachet,
            "carton" => $this->carton,
            "special" => $this->special ? money($this->special) : false,
            "special_not_formatted" =>  $this->special,
            "doorstep" => $this->doorstep ? money($this->doorstep) : false,
            "doorstep_not_formatted" => $this->doorstep,
            "expiry_date" => $this?->{ApplicationEnvironment::$stock_model_string}?->expiry_date?->format("F jS, Y"),
        ];
    }
}
