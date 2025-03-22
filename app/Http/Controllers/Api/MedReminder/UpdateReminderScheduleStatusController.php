<?php

namespace App\Http\Controllers\Api\MedReminder;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\MedReminder\MedReminderRequest;
use App\Http\Resources\Api\MedReminder\MedReminderResource;
use App\Http\Resources\Api\MedReminder\MedReminderScheduleResource;
use App\Models\MedReminder;
use App\Models\MedReminderSchedule;
use App\Services\Api\MedReminder\MedReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateReminderScheduleStatusController extends ApiController
{
    public MedReminderService  $medReminderService;


    public function __construct(MedReminderService $medReminderService)
    {
        $this->medReminderService = $medReminderService;
    }


    /**
     * @param MedReminderSchedule $medReminderSchedule
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(MedReminderSchedule $medReminderSchedule, Request $request) : JsonResponse
    {
        $medReminderSchedule = $this->medReminderService->updateSchedule($medReminderSchedule, $request->all());
        return $this->showOne(new MedReminderScheduleResource($medReminderSchedule));
    }
}
