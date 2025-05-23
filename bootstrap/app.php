<?php

use App\Exceptions\PsgdcExceptionsHandler;
use App\Http\Middleware\CustomSuperMarketMiddleware;
use App\Http\Middleware\DataPushApiMiddleware;
use App\Http\Middleware\DetectWholesalesSalesRepresentativesImpersonation;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\DetectApplicationEnvironment;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (){
            Route::middleware([DetectApplicationEnvironment::class, 'web'])->group(base_path("routes/auth.php"));

            Route::middleware(['auth',DetectApplicationEnvironment::class, 'web','verified'])
                ->domain(config('app.SUPERMARKET_ADMIN'))
                ->name(config('app.SUPERMARKET_ADMIN_ROUTE_PREFIX'))
                ->group(base_path("routes/admin_general.php"))
                ->group(base_path("routes/supermaket.admin.php"));


            Route::middleware(['auth',DetectApplicationEnvironment::class, 'web','verified'])
                ->domain(config('app.WHOLESALES_ADMIN'))
                ->name(config('app.WHOLESALES_ADMIN_ROUTE_PREFIX'))
                ->group(base_path("routes/admin_general.php"))
                ->group(base_path("routes/wholesales.admin.php"));

            Route::middleware(['web'])
                ->group(base_path("routes/utilities.php"));

            /**  API ROUTE STATE HERE */
            Route::middleware(['api',DetectApplicationEnvironment::class, ForceJsonResponse::class , DataPushApiMiddleware::class])
                ->domain(config('app.PUSH_DOMAIN'))
                ->group(base_path("routes/push.api.php"));

            Route::middleware([CustomSuperMarketMiddleware::class, DetectApplicationEnvironment::class, 'api', ForceJsonResponse::class])
                ->prefix('api/v1')
                ->domain(config('app.SUPERMARKET_DOMAIN'))
                ->namespace('App\Http\Controllers\Api')
                ->name(config('app.SUPERMARKET_DOMAIN_ROUTE_PREFIX'))
                ->group(base_path("routes/api_general.php"))
                ->group(base_path("routes/supermarket.api.php"));

            Route::middleware(['auth:sanctum', DetectWholesalesSalesRepresentativesImpersonation::class, DetectApplicationEnvironment::class,'api',ForceJsonResponse::class])
                ->prefix('api/v1')
                ->domain(config('app.WHOLESALES_DOMAIN'))
                ->name(config('app.WHOLESALES_DOMAIN_ROUTE_PREFIX'))
                ->namespace('App\Http\Controllers\Api')
                ->group(base_path("routes/api_general.php"))
                ->group(base_path("routes/wholesales.api.php"));

            Route::middleware(['api', DetectApplicationEnvironment::class,ForceJsonResponse::class])
                ->prefix('api/v1')
                ->domain(config('app.SALES_REPRESENTATIVES'))
                ->name(config('app.SALES_REPRESENTATIVES_ROUTE_PREFIX'))
                ->namespace('App\Http\Controllers\Api')
                ->group(base_path("routes/sales_rep.php"));


            Route::middleware(['api',DetectApplicationEnvironment::class, ForceJsonResponse::class])
                ->prefix('api/v1')
                ->domain(config('app.AUTH_DOMAIN'))
                ->namespace('App\Http\Controllers\Api')
                ->group(base_path("routes/auth.api.php"));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(NotFoundHttpException | ModelNotFoundException | ValidationException | AuthenticationException  | Exception $e, Request $request){
            return PsgdcExceptionsHandler::handleExceptions($e, $request);
        });
    })->create();
