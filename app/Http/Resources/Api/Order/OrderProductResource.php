<?php

namespace App\Http\Resources\Api\Order;

use App\Classes\ApplicationEnvironment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            "name" => $this->stock->name,
            "price" => money($this->price),
            "price_not_formatted" => $this->price,
            "total" => money($this->total),
            "total_not_formatted" => $this->total,
            "quantity" => $this->quantity,
            "image" => $this->stock->product_image,
        ];
    }
}
