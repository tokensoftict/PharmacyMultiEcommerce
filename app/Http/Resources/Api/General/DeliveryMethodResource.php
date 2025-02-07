<?php

namespace App\Http\Resources\Api\General;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryMethodResource extends JsonResource
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
            "name"=> $this->name,
            "description" => $this->description,
            "path"=> $this->path,
            "code"=> $this->code,
            "status"=> $this->status,
            "template_settings" => $this->template_settings,
            "template_settings_value" => $this->template_settings_value,
            "checkout_template" => $this->checkout_template,
            "isDefault" => $this->isDefault()
        ];
    }
}
