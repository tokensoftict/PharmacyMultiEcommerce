<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;


class ForgotPasswordController extends ApiController
{

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function __invoke(ForgotPasswordRequest $request) : JsonResponse
    {
        $credentials = is_numeric($request->get("email_or_phone")) ? ['phone' => $request->get("email_or_phone")] : ['email' => $request->get("email_or_phone")];

        $status = Password::sendResetLink(
            $credentials
        );

        if($status == Password::RESET_THROTTLED){
            return $this->sendErrorResponse("Too many request, Please wait for 120 seconds before trying again", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(is_numeric($request->get("email_or_phone"))){
            if($status != Password::RESET_LINK_SENT){
                return $this->sendErrorResponse("We are unable to locate your phone number on our system", Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $message = is_numeric($request->get("email_or_phone")) ? "You will receive text message from us, with the instruction on how to reset your password, if your phone number is found" : "You will receive an email from us, with the instruction on how to reset your password, if your email address is found";
        return $this->sendSuccessResponse(["message" => $message]);
    }
}
