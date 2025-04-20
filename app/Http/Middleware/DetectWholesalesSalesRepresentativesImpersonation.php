<?php

namespace App\Http\Middleware;

use App\Models\WholesalesUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectWholesalesSalesRepresentativesImpersonation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if($request->headers->has('impersonation-wholesales-id')) {
            $wholesalesUser = WholesalesUser::with('user')->find($request->headers->get('impersonation-wholesales-id'));
            if($wholesalesUser) {
                $request->setUserResolver(function () use ($wholesalesUser) {
                    return $wholesalesUser->user;
                });

                auth('sanctum')->setUser($wholesalesUser->user);
            }
        }
        return $next($request);
    }
}
