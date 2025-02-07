<?php

namespace App\Http\Resources\Api\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressListResource extends JsonResource
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
            "address_1" => $this->address_1,
            "address_2" => $this->address_2,
            "country" => $this->country?->name,
            "state" => $this->state?->name,
            "town" => $this->town?->name,
            "isDefault" => $this->isDefault(),
            "stateObject" => ['id' => $this->state?->id, 'name' =>$this->state?->name],
            "countryObject" => ['id' => $this->country?->id, 'name' =>$this->country?->name],
            "townObject" => ['id' =>  $this->town?->id, 'name' => $this->town?->name],
        ];
    }
}
