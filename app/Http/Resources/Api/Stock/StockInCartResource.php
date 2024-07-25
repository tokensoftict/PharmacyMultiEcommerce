<?php

namespace App\Http\Resources\Api\Stock;

use App\Classes\ApplicationEnvironment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockInCartResource extends JsonResource
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
            "description" => $this->description,
            "price" => number_format($this->price, 2),
            "price_not_formatted" => $this->price,
            "total" => number_format($this->total,2),
            "total_not_formatted" => $this->total,
            "cart_quantity" => $this->cart_quantity,
            "quantity" => $this->{ApplicationEnvironment::$stock_model_string}->quantity,
            "added_date" => $this->added_date,
            "productcategory" => $this->productcategory?->name,
            "manufacturer" => $this->manufacturer?->name,
            "classification" => $this->classification?->name,
            "productgroup_id" => $this->productgroup?->name,
            "image" => $this->product_image,
            "max" => $this->max,
            "box" => $this->box,
            "sachet" => $this->sachet,
            "carton" => $this->carton,
            "special" => number_format(rand(3000, 5000),2),
            "special_not_formatted" =>  1,
            "doorstep" => number_format(rand(100.5, 200.5),2),
            "expiry_date" => $this?->expiry_date?->format("F jS, Y"),
        ];
    }
}
