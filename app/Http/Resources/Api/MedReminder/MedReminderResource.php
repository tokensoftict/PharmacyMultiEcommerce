<?php

namespace App\Http\Resources\Api\MedReminder;

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
            'normal_schedules' => $this->normal_schedules,
            'type' => $this->type,
            'use_interval' => $this->use_interval,
            'interval' => $this->interval,
            'every' => $this->every,
            'start_date_time' => $this->start_date_time,
            'date_create' => $this->date_create,
            'notes' => $this->notes,
            'med_reminder_schedules' => MedReminderScheduleResource::collection($this->med_reminder_schedules)
        ];
    }
}
