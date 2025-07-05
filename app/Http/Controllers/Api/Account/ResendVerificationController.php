<?php

namespace App\Http\Controllers\Api\Account;

use App\Classes\NotificationManager\NewAccountNotificationManager;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\ResendVerificationRequest;
use App\Notifications\NewAccountRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResendVerificationController extends ApiController
{
    /**
     * @param ResendVerificationRequest $request
     * @return JsonResponse
     */
    public function __invoke(ResendVerificationRequest $request) : JsonResponse
    {
        $user = $request->user();
        NewAccountNotificationManager::applyResendVerificationToNotification($user);
        $message = "";

        //if verifying phone number
        if($request->has("phone")){

            if($user->hasVerifiedPhone()){
                return $this->sendSuccessResponse(["hasVerified" => false, "message" => "Your Phone Number has already been verified"]);
            }

            $user->notify(new NewAccountRegistration(true, false));
            $message = "Otp has been sent successfully";

            $request->request->add(["email" => $user->email]);
        }

        //if resetting email address

        if($request->has("email")){

            if($user->hasVerifiedEmail()){
                return $this->sendSuccessResponse(["hasVerified" => true, "message" => "Your Email Address has already been verified"]);
            }

            $user->notify(new NewAccountRegistration(false, true));
            $message = "We have sent email verification instruction to your mailbox";
        }

        return $this->sendSuccessResponse(["hasVerified" => false, 'message' => $message]);
    }
}
