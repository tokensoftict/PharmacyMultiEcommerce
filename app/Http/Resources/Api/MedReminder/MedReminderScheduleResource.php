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
            "drugName" => $this->med_reminder->drug_name ?? "",
            "med_reminder_id" => $this->med_reminder_id,
            "dosage" => $this->med_reminder->dosage ?? 0,
            "dosage_form" => $this->med_reminder->dosage_form ?? "",
            "title" => $this->title,
            "status" => $this->status,
            "snoozed_at" => $this->snoozed_at?->format('h:i A'),
            "scheduled_at" => $this->snoozed_at ? $this->snoozed_at?->format('h:i A') : $this->scheduled_at->format('h:i A'),
            "scheduled_at_full" => $this->snoozed_at ? $this->snoozed_at->format('j M Y h:i A') :  $this->scheduled_at->format('j M Y h:i A'),
            "js_date" => $this->snoozed_at ? $this->snoozed_at->format("Y-m-d H:i") : $this->scheduled_at->format('Y-m-d H:i'),
            "snoozed_at_full" =>  $this->snoozed_at?->format('j M Y h:i A'),
            "med_reminder" => $this->med_reminder ?? new MedReminderResource($this->med_reminder),
            "allowTaken" => $this->checkIFDrugIsReadyToBeTaken()
        ];
    }


    private function checkIFDrugIsReadyToBeTaken() : bool
    {
        if(!empty($this->snoozed_at)){
           return carbonize($this->snoozed_at)->lessThanOrEqualTo(now());
        }

        return carbonize($this->scheduled_at)->lessThanOrEqualTo(now());
    }
}
