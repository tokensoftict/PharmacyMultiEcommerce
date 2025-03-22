<?php

namespace App\Http\Controllers\Api\MedReminder;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\MedReminder\MedReminderRequest;
use App\Http\Resources\Api\MedReminder\MedReminderResource;
use App\Http\Resources\Api\MedReminder\MedReminderScheduleResource;
use App\Models\MedReminder;
use App\Services\Api\MedReminder\MedReminderService;
use Illuminate\Http\JsonResponse;

class ListAllSchedulesController extends ApiController
{
    public MedReminderService  $medReminderService;


    public function __construct(MedReminderService $medReminderService)
    {
        $this->medReminderService = $medReminderService;
    }


    /**
     * @return JsonResponse
     */
    public function __invoke() : JsonResponse
    {
        return $this->sendSuccessResponse(
            MedReminderScheduleResource::collection(
                $this->medReminderService->listSchedules(request()->user())
            )
        );
    }
}
