<?php

namespace App\Http\Controllers\Api\Account;

use App\Events\Auth\PhoneVerified;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PhoneNumberVerificationRequest;
use App\Notifications\NewAccountRegistration;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PhoneNumberVerificationController extends  ApiController
{
    /**
     * @param PhoneNumberVerificationRequest $request
     * @return JsonResponse
     */
    public function __invoke(PhoneNumberVerificationRequest $request) : JsonResponse
    {
        $user = auth("sanctum")->user();

        if($user->hasVerifiedPhone())
        {
            return $this->sendSuccessResponse(["message" => "Your phone number has been verified successfully!"]);
        }

        $status = $user->markPhoneAsVerified($request->get("otp"));

        if($status) {
            event(new PhoneVerified($user));
            return $this->sendSuccessResponse(["message" => "Your phone number has been verified successfully!"]);
        }

        return $this->errorResponse("Invalid otp, Please check and try again", Response::HTTP_BAD_REQUEST);
    }


}
