<?php

namespace App\Http\Middleware;

use App\Classes\ApplicationEnvironment;
use App\Models\App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class DetectApplicationEnvironment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->getHost();
        $app = App::where("domain", $domain)->first();
        if(!$app)  throw new UnexpectedValueException('Unable to create application environment');

        ApplicationEnvironment::createApplicationEnvironment($app);

        return $next($request);
    }
}
