<?php

namespace app\Http\Controllers\Api\MedReminder;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\MedReminder\MedReminderRequest;
use App\Http\Resources\Api\MedReminder\MedReminderResource;
use App\Http\Resources\Api\MedReminder\MedReminderScheduleResource;
use App\Models\MedReminder;
use App\Services\Api\MedReminder\MedReminderService;
use Illuminate\Http\JsonResponse;

class RemoveReminderController extends ApiController
{
    public MedReminderService  $medReminderService;


    public function __construct(MedReminderService $medReminderService)
    {
        $this->medReminderService = $medReminderService;
    }

    /**
     * @param MedReminder $medReminder
     * @return JsonResponse
     */
    public function __invoke(MedReminder $medReminder) : JsonResponse
    {
        $schedules = MedReminderScheduleResource::collection($medReminder->med_reminder_schedules()->get());
        $this->medReminderService->delete($medReminder);
        return $this->sendSuccessResponse($schedules);
    }
}
