<?php

namespace App\Http\Controllers\Utilities\Json\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Utilities\Json\User\UserSearchResource;
use App\Models\User;
use App\Models\WholesalesUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserUtilitiesController extends Controller
{

    public final function searchForUser(Request $request) : AnonymousResourceCollection | JsonResponse
    {
        $searchTerm  = $request->get('searchTerm') ?? $request->get('s');
        if($searchTerm == "") return response()->json([], 200);
        return UserSearchResource::collection(
            User::query()->select('id', 'firstname', 'lastname', \DB::raw("CONCAT(firstname, ' ', lastname) AS name"))
            ->where(function($search) use($searchTerm){
                $search->orWhere('firstname', 'LIKE', "%{$searchTerm}%");
                $search->orWhere('lastname', 'LIKE', "%{$searchTerm}%");
                $search->orWhere('email', 'LIKE', "%{$searchTerm}%");
                $search->orWhere('phone', 'LIKE', "%{$searchTerm}%");
            })->whereNotNull('email_verified_at')->get()
        );
    }


    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public final function searchWholesalesCustomers(Request $request) : AnonymousResourceCollection | JsonResponse
    {
        $searchTerm  = $request->get('searchTerm') ?? $request->get('s');
        if($searchTerm == "") return response()->json([], 200);
        return UserSearchResource::collection(
            WholesalesUser::query()
                ->select('wholesales_users.id AS id',  \DB::raw("wholesales_users.business_name AS name"))
            ->join('users', 'users.id', '=', 'wholesales_users.user_id')
            ->where(function($search) use($searchTerm){
                $search->orWhere('users.firstname', 'LIKE', "%{$searchTerm}%");
                $search->orWhere('users.lastname', 'LIKE', "%{$searchTerm}%");
                $search->orWhere('users.email', 'LIKE', "%{$searchTerm}%");
                $search->orWhere('users.phone', 'LIKE', "%{$searchTerm}%");
                $search->orWhere('wholesales_users.business_name', 'LIKE', "%{$searchTerm}%");
                $search->orWhere('wholesales_users.phone', 'LIKE', "%{$searchTerm}%");
            })->get()
        );
    }

}
