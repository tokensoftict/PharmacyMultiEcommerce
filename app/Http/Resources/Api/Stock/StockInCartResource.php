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
            "price" => money($this->{ApplicationEnvironment::$stock_model_string}->price),
            "price_not_formatted" => $this->{ApplicationEnvironment::$stock_model_string}->price,
            "total" => money($this->total),
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
            "special" => $this->special ? money($this->special) : false,
            "special_not_formatted" =>  $this->special,
            "doorstep" => $this->doorstep ? money($this->doorstep) : false,
            "doorstep_not_formatted" => $this->doorstep,
            "expiry_date" => $this?->expiry_date?->format("F jS, Y"),
            "custom_price" => $this?->stockquantityprices?->map?->only(['price', 'min_qty', 'max_qty'])->toArray() ?? []
        ];
    }
}
