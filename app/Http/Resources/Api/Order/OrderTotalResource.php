<?php

namespace App\Http\Resources\Api\Order;

use App\Classes\ApplicationEnvironment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTotalResource extends JsonResource
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
            "value" => $this->value,
            "value_formatted" => money($this->value)
        ];
    }
}
