<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Auth\UserLoginResource;
use App\Models\WholesalesUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateCustomerTypeController extends ApiController
{
    /**
     * Update customer_type_id for the authenticated wholesale user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'customer_type_id' => 'required|integer|exists:customer_types,id',
        ]);

        $user = $request->user();
        $wholesaleUser = WholesalesUser::where('user_id', $user->id)->first();

        if ($wholesaleUser) {
            $wholesaleUser->customer_type_id = (int) $request->input('customer_type_id');
            $wholesaleUser->save();
        }

        return $this->showOne(
            new UserLoginResource($user->fresh())
        );
    }
}
