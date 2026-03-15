<?php

namespace App\Http\Resources\Api\Stock;

use App\Classes\ApplicationEnvironment;
use App\Traits\StockResourceHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockInCartResource extends JsonResource
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
            "description" => $this->description,
            "price" => money($this->price ?? $this->{ApplicationEnvironment::$stock_model_string}->price),
            "price_not_formatted" => $this->price ?? $this->{ApplicationEnvironment::$stock_model_string}->price,
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
            "expiry_date" => $this?->expiry_date?->format("d M Y"),
            "is_dependent" => (bool) ($this->is_dependent ?? false),
            "parent_stock_id" => $this->parent_stock_id ?? null,
            "custom_price" => $this->filterCustomPrices($this->resource),
            "stock_option_values" => $this->filterStockOptions($this->resource),
            "selected_options" => $this->selected_options ?? [],
            "dependent_products" => $this->getDependentProducts($this->resource),
        ];
    }
}
