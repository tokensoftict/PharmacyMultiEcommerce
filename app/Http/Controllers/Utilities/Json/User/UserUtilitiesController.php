<?php

namespace App\Http\Controllers\Utilities\Json\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Utilities\Json\User\UserSearchResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserUtilitiesController extends Controller
{

    public final function searchForUser(Request $request) : AnonymousResourceCollection
    {
        $searchTerm  = $request->get('searchTerm') ?? $request->get('s');
        return UserSearchResource::collection(
            User::query()->select('id', 'firstname', 'lastname')
            ->where(function($search) use($searchTerm){
                $search->orwhere('firstname', 'LIKE', "%{$searchTerm}%");
                $search->orwhere('lastname', 'LIKE', "%{$searchTerm}%");
                $search->orwhere('email', 'LIKE', "%{$searchTerm}%");
            })->whereNotNull('email_verified_at')->get()
        );
    }

}
