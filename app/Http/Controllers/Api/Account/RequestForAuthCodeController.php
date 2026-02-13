<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\RequestForAuthCodeRequest;
use App\Mail\Customer\AuthCodeEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RequestForAuthCodeController extends ApiController
{

    /**
     * @param RequestForAuthCodeRequest $request
     * @return JsonResponse
     */
    public function __invoke(RequestForAuthCodeRequest $request) : JsonResponse
    {
        $column = is_numeric($request->email) ? "phone" : "email";
        $what = is_numeric($request->email) ? "Phone number" : "Email address";


        $user = User::query()->where($column, $request->email)->first();
        $status = false;

        if(!$user){
            throw ValidationException::withMessages([
                'email' => "The $what entered does not match our records.",
            ]);
        }

        if(is_numeric($request->email)){
            $status = $this->sendAuthCodeToPhoneNumber($user);
        }

        if(!is_numeric($request->email)){
            $status = $this->sendAuthCodeToEmailAddress($user);
        }

        if(!$status) {
            throw ValidationException::withMessages([
                'email' => "There was error sending OTP to $what, please try again.",
            ]);
        }

        $user->save();

        return $this->sendSuccessMessageResponse("We have sent code to your $what");
    }


    private function sendAuthCodeToEmailAddress(User &$user) : bool
    {
        $otp = mt_rand(1000, 9999);
        $user->auth_code = $otp;
        Mail::to($user->email)->send(new AuthCodeEmail($user, $otp));
        return true;
    }


    private function sendAuthCodeToPhoneNumber(User &$user) : bool
    {
        $otp = mt_rand(1000, 9999);
        $user->auth_code = $otp;
        $message = "Your login code is $otp. It will expire in 10 minutes. Ignore if you didn’t request this.";
        $response = sendSMS($user->phone, $user, $message);
        $response = is_array($response) ? $response : json_decode($response, true);
        return true;
    }
}
