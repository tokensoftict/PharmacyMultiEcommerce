<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class QRCodeController extends ApiController
{

    /**
     * @param Request $request
     * @return string
     */
    public function __invoke(Request $request) :JsonResponse
    {
        $user = User::find($request->get('id'));

        $information = [
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'phone' => $user->phone,
            'email' => $user->email,
            'id' => $user->id,
        ];

        @unlink(public_path('qrcodes/' . $user->id . '.png'));

        $image = QrCode::format('png')
            ->size(800)
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->margin(1)
            ->generate(json_encode($information));

        if (!file_exists(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0777, true);
        }

        $savePath = public_path('qrcodes/' . $user->id . '.png');

        file_put_contents($savePath, $image);

        return $this->sendSuccessResponse([
            'qrcode' => asset('qrcodes/' . $user->id . '.png'),
        ]);
    }
}
