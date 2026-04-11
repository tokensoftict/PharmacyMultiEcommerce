<?php

namespace App\Http\Resources\Api\Stock;

use App\Classes\ApplicationEnvironment;
use App\Traits\StockResourceHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchStockResource extends JsonResource
{
    use StockResourceHelper;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $wholesales = $this->wholessales_stock_prices;
        $retail = $this->supermarkets_stock_prices;

        return [
            "id" => $this->id,
            "name" => $this->name,
            "image" => $this->product_image,
            "wholesales" => $wholesales ? [
                "price" => money($wholesales->price),
                "price_not_formatted" => $wholesales->price,
                "quantity" => $wholesales->quantity,
                "expiry_date" => $wholesales->expiry_date?->format("d M Y"),
                "status" => $wholesales->status,
            ] : null,
            "retail" => $retail ? [
                "price" => money($retail->price),
                "price_not_formatted" => $retail->price,
                "quantity" => $retail->quantity,
                "expiry_date" => $retail->expiry_date?->format("d M Y"),
                "status" => $retail->status,
            ] : null,
            "max" => $this->max,
            "box" => $this->box,
            "sachet" => $this->sachet,
            "carton" => $this->carton,
            "is_wholesales" => $this->is_wholesales,
            "admin_status" => $this->admin_status,
        ];
    }
}
