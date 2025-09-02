<?php

namespace App\Http\Resources\Api\Stock;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockStoreResource extends JsonResource
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
            "stock_id" => $this->stock_id,
            "app_id" => $this->app_id,
            "status" => $this->status,
            "quantity" => $this->quantity,
            "featured" => $this->featured,
            "special_offer" => $this->special_offer,
            "price"=> $this->price,
            "expiry_date"=> $this?->expiry_date?->format("F jS, Y") ?? false,
            "custom_price" => $this?->stockquantityprices?->map(function ($item) {
                return $item->only(['price', 'min_qty', 'max_qty']) + [
                        'price_formatted' => money($item->price),
                    ];
            })->toArray()
        ];
    }
}
