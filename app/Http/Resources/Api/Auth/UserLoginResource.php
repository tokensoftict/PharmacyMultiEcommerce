<?php

namespace App\Http\Resources\Api\Auth;

use App\Http\Resources\Api\MedReminder\MedReminderScheduleResource;
use App\Models\App;
use App\Models\AppUser;
use App\Services\Api\MedReminder\MedReminderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;


class UserLoginResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = [
            "id" => $this->id,
            "name" => $this->name,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "email_verification_status" => !is_null($this->email_verified_at),
            "phone_verified_status" => !is_null($this->phone_verified_at),
            "phone" => $this->phone,
            "image" => asset($this->image),
            "medSchedules" => MedReminderScheduleResource::collection($this->medReminderSchedule()),
            'dosageForms' => MedReminderService::$dosageForm
        ];

        $apps = [];

        $frontEndApps = AppUser::where("user_id", $this->id)
            ->whereHas("app", function ($query){
                $query->where("type", "Frontend");
            })->orderBy('app_id', 'desc')->get();

        foreach ($frontEndApps as $app)
        {
            if($app->app->id === 4) {
                if($app->user_type->status == "0" || $app->user_type->invitation_status == "0") continue;
            }
            $apps[] = [
                "app_id" => $app->id,
                "domain" => $app->domain,
                "description" => $app->app->description,
                "info" => $app->user_type,
                "logo" => $app->app->logo,
                "name" => strtolower($app->app->name),
                "link" => $app->app->link,
                "addresses" => $app->addresses,
                "last_seen" => $app->last_activity_date ? $app->last_activity_date->format("F jS, Y g:i A") :  now()->format("F jS, Y g:i A"),
                "unregistered" => false,
                "status" => $app->status,
            ];
        }

        $user['apps'] = $apps;

        if(count($user['apps']) === 1) {
            $wholesales = App::find(5);
            $user['apps'][] = [
                "app_id" => $wholesales->id,
                "domain" => $wholesales->domain,
                "description" => $wholesales->description,
                "info" => [],
                "logo" => $wholesales->logo,
                "name" => strtolower($wholesales->name),
                "link" => $wholesales->link,
                "addresses" => [],
                "last_seen" => false,
                "unregistered" => true,
                "status" => false
            ];
        }


        if(!is_null($this->currentAccessToken())) {
            $user['token'] = [
                'token_type' => 'bearer',
                'access_token' => Str::replace("Bearer ","", $request->headers->get("authorization"))
            ];
        }else{
            $user['token'] = [
                'token_type' => 'bearer',
                'access_token' => $this->createToken(config("app.name"))->plainTextToken
            ];
        }

        $user['systemSettings'] = [
            'mustVerify' => config("app.mustVerify"),
            'verifyField' => config("app.mustVerify") === "email" ? "email_verification_status" : "phone_verified_status",
        ];

        return $user;
    }
}
