<?php

namespace App\Http\Resources\Api\Stock;

use App\Classes\ApplicationEnvironment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockListJoinResource extends JsonResource
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
            "price" => money($this->price),
            "price_not_formatted" => $this->price,
            "image" => $this->product_image,
            "quantity" => $this->quantity,
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
