<?php

namespace App\Http\Controllers\Api\Account;

use App\Events\Auth\EmailVerified;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\EmailAddressVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EmailAddressVerificationController extends  ApiController
{
    /**
     * @param EmailAddressVerificationRequest $request
     * @return JsonResponse
     */
    public function __invoke(EmailAddressVerificationRequest $request) : JsonResponse
    {
        $user = auth("sanctum")->user();

        if($user->hasVerifiedEmail())
        {
            return $this->sendSuccessResponse(["message" => "Your email address has been verified successfully!"]);
        }

        $status = $user->markEmailAsVerified();

        if($status) {
            event(new Verified($request->user())); // run default verification
            event(new EmailVerified($request->user()));
            return $this->sendSuccessResponse(["message" => "Your email address has been verified successfully!"]);
        }

        return $this->errorResponse("Invalid otp, Please check and try again", Response::HTTP_BAD_REQUEST);
    }


}
