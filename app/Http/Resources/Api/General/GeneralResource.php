<?php

namespace App\Http\Resources\Api\General;

use App\Http\Resources\Api\Stock\StockListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this?->image
        ];

        if($this->relationLoaded('stocks')){
            $data['stocks'] = StockListResource::collection($this->stocks);
        }

        return $data;
    }
}
