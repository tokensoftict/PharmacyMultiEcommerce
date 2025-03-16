<?php

namespace App\Http\Resources\Api\MedReminder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedReminderScheduleResource extends JsonResource
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
            "drugName" => $this->med_reminder->drug_name,
            "med_reminder_id" => $this->med_reminder_id,
            "dosage" => $this->med_reminder->dosage,
            "title" => $this->title,
            "status" => $this->status,
            "snoozed_at" => $this->snoozed_at?->format('h:i A'),
            "scheduled_at" => $this->scheduled_at->format('h:i A')
        ];
    }
}
