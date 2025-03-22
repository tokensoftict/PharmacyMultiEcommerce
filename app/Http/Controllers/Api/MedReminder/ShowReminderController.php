<?php

namespace App\Http\Controllers\Api\MedReminder;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\MedReminder\MedReminderRequest;
use App\Http\Resources\Api\MedReminder\MedReminderResource;
use App\Http\Resources\Api\MedReminder\MedReminderScheduleResource;
use App\Models\MedReminder;
use App\Services\Api\MedReminder\MedReminderService;
use Illuminate\Http\JsonResponse;

class ShowReminderController extends ApiController
{
    public MedReminderService  $medReminderService;

    /**
     * @param MedReminder $medReminder
     * @return JsonResponse
     */
    public function __invoke(MedReminder $medReminder) : JsonResponse
    {
        return $this->sendSuccessResponse([
            'medReminder' => new MedReminderResource($medReminder),
            'schedules' => MedReminderScheduleResource::collection($medReminder->med_reminder_schedules)
        ]);
    }
}
