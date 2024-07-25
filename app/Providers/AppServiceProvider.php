<?php

namespace App\Providers;

use App\Classes\Settings;
use App\Http\Middleware\DetectApplicationEnvironment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire;
use Spatie\Valuestore\Valuestore;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(Settings::class, function () {
            return Settings::make(storage_path('app/settings.json'));
        });

        Livewire::addPersistentMiddleware([
            DetectApplicationEnvironment::class
        ]);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Administrator') ? true : null;
        });
    }
}
