<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class LoginWithVerificationToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!auth()->user()){
            if($request->get("auth_token")) {
                $jsonUser = json_decode(decrypt($request->get("auth_token")), true);
                if (isset($jsonUser['email'])) {
                    $user = User::where("email", $jsonUser['email'])
                        ->where("verification_token", $request->get(""))->first();

                    if ($user) {
                        auth()->login($user);
                        Session::regenerate();
                        return $next($request);
                    }
                }
            }
        }
        return $next($request);
    }
}
