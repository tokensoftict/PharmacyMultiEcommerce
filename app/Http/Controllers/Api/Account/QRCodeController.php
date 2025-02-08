<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends ApiController
{

    public function __invoke(Request $request)
    {
        $user = User::find($request->get('id'));

        $information = [
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'phone' => $user->phone,
            'email' => $user->email,
            'id' => $user->id,
        ];

        return QrCode::size(500)
            ->backgroundColor(255, 255, 255)
            ->margin(1)
            ->generate(
                json_encode($information),
            );
    }
}
