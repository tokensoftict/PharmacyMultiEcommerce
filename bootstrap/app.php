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
        then: function () {

            /*
            |--------------------------------------------------------------------------
            | AUTH ROUTES
            |--------------------------------------------------------------------------
            */
            Route::middleware([DetectApplicationEnvironment::class, 'web'])
                ->group(base_path("routes/auth.php"));

            Route::middleware([DetectApplicationEnvironment::class, 'web'])
                ->group(base_path("routes/wholesales.php"));

            Route::middleware([DetectApplicationEnvironment::class, 'web'])
                ->group(base_path("routes/supermarket.php"));
            /*
            |--------------------------------------------------------------------------
            | SHARED MIDDLEWARE DEFINITIONS
            |--------------------------------------------------------------------------
            */
            $webAuthMiddleware = [
                'auth',
                DetectApplicationEnvironment::class,
                'web',
                'verified'
            ];

            /*
            |--------------------------------------------------------------------------
            | SAFE DOMAIN ROUTE REGISTRAR
            |--------------------------------------------------------------------------
            */
            $registerDomainRoutes = function (
                $domains,
                array $middleware,
                string $namePrefix,
                array $files,
                ?string $prefix = null,
                ?string $namespace = null
            ) {
                // ensure it's always iterable
                $domains = is_array($domains) ? $domains : [$domains];

                foreach ($domains as $domain) {

                    // skip invalid empty values (VERY IMPORTANT)
                    if (!$domain) continue;

                    foreach ($files as $file) {

                        $route = Route::middleware($middleware)
                            ->domain($domain)
                            ->name($namePrefix);

                        if ($prefix) {
                            $route->prefix($prefix);
                        }

                        if ($namespace) {
                            $route->namespace($namespace);
                        }

                        $route->group(base_path($file));
                    }
                }
            };

            /*
            |--------------------------------------------------------------------------
            | ADMIN (SUPERMARKET)
            |--------------------------------------------------------------------------
            */
            $registerDomainRoutes(
                config('app.SUPERMARKET_ADMIN'),
                $webAuthMiddleware,
                config('app.SUPERMARKET_ADMIN_ROUTE_PREFIX'),
                [
                    "routes/admin_general.php",
                    "routes/supermaket.admin.php",
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | ADMIN (WHOLESALES)
            |--------------------------------------------------------------------------
            */
            $registerDomainRoutes(
                config('app.WHOLESALES_ADMIN'),
                $webAuthMiddleware,
                config('app.WHOLESALES_ADMIN_ROUTE_PREFIX'),
                [
                    "routes/admin_general.php",
                    "routes/wholesales.admin.php",
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | UTILITIES
            |--------------------------------------------------------------------------
            */
            Route::middleware(['web'])
                ->group(base_path("routes/utilities.php"));

            /*
            |--------------------------------------------------------------------------
            | PUSH API
            |--------------------------------------------------------------------------
            */
            foreach (config('app.PUSH_DOMAIN') as $domain) {

                if (!$domain) continue;

                Route::middleware([
                    'api',
                    DetectApplicationEnvironment::class,
                    ForceJsonResponse::class,
                    DataPushApiMiddleware::class
                ])
                    ->domain($domain)
                    ->group(base_path("routes/push.api.php"));
            }

            /*
            |--------------------------------------------------------------------------
            | SUPERMARKET API
            |--------------------------------------------------------------------------
            */
            foreach (config('app.SUPERMARKET_DOMAIN') as $domain) {

                if (!$domain) continue;

                Route::middleware([
                    CustomSuperMarketMiddleware::class,
                    DetectApplicationEnvironment::class,
                    'api',
                    ForceJsonResponse::class
                ])
                    ->prefix('api/v1')
                    ->domain($domain)
                    ->namespace('App\Http\Controllers\Api')
                    ->name(config('app.SUPERMARKET_DOMAIN_ROUTE_PREFIX'))
                    ->group(base_path("routes/api_general.php"));

                Route::middleware([
                    CustomSuperMarketMiddleware::class,
                    DetectApplicationEnvironment::class,
                    'api',
                    ForceJsonResponse::class
                ])
                    ->prefix('api/v1')
                    ->domain($domain)
                    ->namespace('App\Http\Controllers\Api')
                    ->name(config('app.SUPERMARKET_DOMAIN_ROUTE_PREFIX'))
                    ->group(base_path("routes/supermarket.api.php"));
            }

            /*
            |--------------------------------------------------------------------------
            | WHOLESALES API
            |--------------------------------------------------------------------------
            */
            foreach (config('app.WHOLESALES_DOMAIN') as $domain) {

                if (!$domain) continue;

                Route::middleware([
                    'auth:sanctum',
                    DetectWholesalesSalesRepresentativesImpersonation::class,
                    DetectApplicationEnvironment::class,
                    'api',
                    ForceJsonResponse::class
                ])
                    ->prefix('api/v1')
                    ->domain($domain)
                    ->namespace('App\Http\Controllers\Api')
                    ->name(config('app.WHOLESALES_DOMAIN_ROUTE_PREFIX'))
                    ->group(base_path("routes/api_general.php"));

                Route::middleware([
                    'auth:sanctum',
                    DetectWholesalesSalesRepresentativesImpersonation::class,
                    DetectApplicationEnvironment::class,
                    'api',
                    ForceJsonResponse::class
                ])
                    ->prefix('api/v1')
                    ->domain($domain)
                    ->namespace('App\Http\Controllers\Api')
                    ->name(config('app.WHOLESALES_DOMAIN_ROUTE_PREFIX'))
                    ->group(base_path("routes/wholesales.api.php"));
            }

            /*
            |--------------------------------------------------------------------------
            | SALES REP API
            |--------------------------------------------------------------------------
            */
            foreach (config('app.SALES_REPRESENTATIVES') as $domain) {

                if (!$domain) continue;

                Route::middleware([
                    'api',
                    DetectApplicationEnvironment::class,
                    ForceJsonResponse::class
                ])
                    ->prefix('api/v1')
                    ->domain($domain)
                    ->namespace('App\Http\Controllers\Api')
                    ->name(config('app.SALES_REPRESENTATIVES_ROUTE_PREFIX'))
                    ->group(base_path("routes/sales_rep.php"));
            }

            /*
            |--------------------------------------------------------------------------
            | AUTH API
            |--------------------------------------------------------------------------
            */
            foreach (config('app.AUTH_DOMAIN') as $domain) {

                if (!$domain) continue;

                Route::middleware([
                    'api',
                    DetectApplicationEnvironment::class,
                    ForceJsonResponse::class
                ])
                    ->prefix('api/v1')
                    ->namespace('App\Http\Controllers\Api')
                    ->domain($domain)
                    ->group(base_path("routes/auth.api.php"));
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware) {

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(NotFoundHttpException | ModelNotFoundException | ValidationException | AuthenticationException  | Exception $e, Request $request){
            return PsgdcExceptionsHandler::handleExceptions($e, $request);
        });
    })->create();
