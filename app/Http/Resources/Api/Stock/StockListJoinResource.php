<?php

namespace App\Http\Resources\Api\Stock;

use App\Traits\StockResourceHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockListJoinResource extends JsonResource
{
    use StockResourceHelper;

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
            "expiry_date" => $this?->expiry_date?->format("d M Y"),
            "custom_price" => $this->filterCustomPrices($this->resource),
            "stock_options" => $this->filterStockOptions($this->resource),
        ];
    }
}
