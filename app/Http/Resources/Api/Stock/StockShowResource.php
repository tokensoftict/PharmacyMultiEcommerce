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
            "description" => $this->description ?? '',
            "price" => money($this->{ApplicationEnvironment::$stock_model_string}->price),
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
            'totalSold' => $this->order_products->count(),
            'rating' => mt_rand (1*10, 5*10) / 10,
            'reviews' => rand(100, 300),
            "quantity" => $this->{ApplicationEnvironment::$stock_model_string}->quantity,
            "expiry_date" => $this?->{ApplicationEnvironment::$stock_model_string}?->expiry_date?->format("F jS, Y"),
            "special" => $this->special ? money($this->special) : false,
            "special_not_formatted" =>  $this->special,
            "doorstep" => $this->doorstep ? money($this->doorstep) : false,
            "doorstep_not_formatted" => $this->doorstep,
            "store"=> new StockStoreResource( $this->{ApplicationEnvironment::$stock_model_string}),
            "custom_price" => $this?->stockquantityprices?->map?->only(['price', 'min_qty', 'max_qty'])->toArray() ?? []
        ];
    }
}

