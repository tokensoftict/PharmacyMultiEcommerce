<?php

namespace App\Providers;

use App\Classes\Settings;
use App\Http\Middleware\DetectApplicationEnvironment;
use App\Listeners\PushNotificationFailedListener;
use App\Models\Old\RetailCustomer;
use App\Models\Old\User;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire;

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


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Administrator') ? true : null;
        });

        Relation::morphMap([
            'App\\User' => User::class,
            'App\\RetailCustomer' => RetailCustomer::class,
        ]);

        Livewire::addPersistentMiddleware([
            DetectApplicationEnvironment::class
        ]);

        \Event::listen(
            NotificationFailed::class ,
            PushNotificationFailedListener::class
        );
    }
}
