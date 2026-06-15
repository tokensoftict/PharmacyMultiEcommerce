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
        $domain = $this->parseDomain($domain);
        $app = App::where("domain", $domain)->first();
        if(!$app)  throw new UnexpectedValueException('Unable to create application environment');
        ApplicationEnvironment::createApplicationEnvironment($app);
        return $next($request);
    }



    public function parseDomain(string $requestDomain) : string
    {
        $domain = [
            'wa.psgdc.store' => 'wa.psgdc.store',
            'sa.psgdc.store' => 'sa.psgdc.store',
            'wholesales.psgdc.store' => 'wholesales.psgdc.store',
            'supermarket.psgdc.store' => 'supermarket.psgdc.store',
            'psgdc.store' => 'psgdc.store',
            'auth.psgdc.store' => 'auth.psgdc.store',
            'admin.psgdc.store' => 'admin.psgdc.store',
            'pa.psgdc.store' => 'pa.psgdc.store',
            'rep.psgdc.store' => 'rep.psgdc.store',


            'wa.generaldrugcentre.com' => 'wa.psgdc.store',
            'sa.generaldrugcentre.com' => 'sa.psgdc.store',
            'wholesales.generaldrugcentre.com' => 'wholesales.psgdc.store',
            'supermarkets.generaldrugcentre.com' => 'supermarket.psgdc.store',
            'supermarket.generaldrugcentre.com' => 'supermarket.psgdc.store',
            'generaldrugcentre.com' => 'psgdc.store',
            'www.generaldrugcentre.com' => 'psgdc.store',
            'auth.generaldrugcentre.com' => 'auth.psgdc.store',
            'admin.generaldrugcentre.com' => 'admin.psgdc.store',
            'pa.generaldrugcentre.com' => 'pa.psgdc.store',
            'rep.generaldrugcentre.com' => 'rep.psgdc.store',

        ];

        return $domain[$requestDomain] ?? $requestDomain;
    }
}
