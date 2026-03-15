<?php

namespace app\Http\Resources\Api\Stock;

use App\Classes\ApplicationEnvironment;
use App\Traits\StockResourceHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BestSellerStockResource extends JsonResource
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
            "id" => $this->stock->id,
            "name" => $this->stock->name,
            "price" => money($this->stock->{ApplicationEnvironment::$stock_model_string}->price),
            "price_not_formatted" => $this->stock->{ApplicationEnvironment::$stock_model_string}->price,
            "image" => $this->stock->product_image,
            "quantity" => $this->stock->{ApplicationEnvironment::$stock_model_string}->quantity,
            "max" => $this->stock->max,
            "box" => $this->stock->box,
            "sachet" => $this->stock->sachet,
            "carton" => $this->stock->carton,
            "special" => $this->stock->special ? money($this->special) : false,
            "special_not_formatted" =>  $this->stock->special,
            "doorstep" => $this->stock->doorstep ? money($this->stock->doorstep) : false,
            "doorstep_not_formatted" => $this->stock->doorstep,
            "expiry_date" => $this->stock?->{ApplicationEnvironment::$stock_model_string}?->expiry_date?->format("d M Y"),
            "custom_price" => $this->filterCustomPrices($this->stock),
            "stock_option_values" => $this->filterStockOptions($this->stock),
        ];
    }
}
