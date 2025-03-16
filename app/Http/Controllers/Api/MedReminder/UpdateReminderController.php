<?php

namespace app\Http\Controllers\Api\MedReminder;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\MedReminder\MedReminderRequest;
use App\Http\Resources\Api\MedReminder\MedReminderResource;
use App\Models\MedReminder;
use App\Services\Api\MedReminder\MedReminderService;
use Illuminate\Http\JsonResponse;

class UpdateReminderController extends ApiController
{
    public MedReminderService  $medReminderService;


    public function __construct(MedReminderService $medReminderService)
    {
        $this->medReminderService = $medReminderService;
    }

    /**
     * @param MedReminder $medReminder
     * @param MedReminderRequest $request
     * @return JsonResponse
     */
    public function __invoke(MedReminder $medReminder, MedReminderRequest $request) : JsonResponse
    {
        $medReminder =  $this->medReminderService->update($medReminder, $request->all());
        return $this->showOne(new MedReminderResource($medReminder));
    }
}
