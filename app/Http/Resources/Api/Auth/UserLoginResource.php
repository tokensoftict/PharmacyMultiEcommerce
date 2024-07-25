<?php

namespace App\Http\Resources\Api\Auth;

use App\Models\AppUser;
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
        ];

        $apps = [];

        $frontEndApps = AppUser::where("user_id", $this->id)
            ->whereHas("app", function ($query){
                $query->where("type", "Frontend");
            })->get();

        foreach ($frontEndApps as $app)
        {
            $apps[] = [
                "app_id" => $app->id,
                "domain" => $app->domain,
                "info" => $app->user_type,
                "logo" => $app->app->logo,
                "name" => $app->app->name,
                "link" => $app->app->link,
                "addresses" => $app->addresses
            ];
        }

        $user['apps'] = $apps;


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

        return $user;
    }
}
