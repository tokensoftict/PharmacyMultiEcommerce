<?php

namespace App\Http\Resources\Api\MedReminder;

use App\Http\Resources\Api\Stock\StockShowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedReminderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'drug_name' => $this->drug_name,
            'dosage' => $this->dosage,
            'total_dosage_in_package' => $this->total_dosage_in_package,
            'total_dosage_taken' => $this->total_dosage_taken,
            'percentageTaken' => round(($this->total_dosage_taken/$this->total_dosage_in_package) * 100),
            'allowRefill' => ((($this->total_dosage_taken/$this->total_dosage_in_package) * 100) > 80 && !empty($this->stock) && $this->type == "CONTINUES"),
            'normal_schedules' => $this->normal_schedules,
            'type' => $this->type,
            'use_interval' => $this->use_interval,
            'interval' => $this->interval,
            'every' => $this->every,
            'start_date_time' => $this->start_date_time,
            'date_create' => $this->date_create->format('j M Y'),
            'notes' => $this->notes,
            'stock' => $this->stock ? new StockShowResource($this->stock) : null,
        ];
    }
}
