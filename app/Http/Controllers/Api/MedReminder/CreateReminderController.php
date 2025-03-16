<?php

namespace App\Http\Controllers\Api\MedReminder;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\MedReminder\MedReminderRequest;
use App\Http\Resources\Api\MedReminder\MedReminderResource;
use App\Services\Api\MedReminder\MedReminderService;
use Illuminate\Http\JsonResponse;

class CreateReminderController extends ApiController
{
    public MedReminderService  $medReminderService;


    public function __construct(MedReminderService $medReminderService)
    {
        $this->medReminderService = $medReminderService;
    }

    /**
     * @param MedReminderRequest $request
     * @return JsonResponse
     */
    public function __invoke(MedReminderRequest $request) : JsonResponse
    {
        $medReminder =  $this->medReminderService->create($request->all());
        return $this->showOne(new MedReminderResource($medReminder));
    }
}
