<?php

namespace App\Providers;

use App\Services\Campaign\CampaignConditionsService;
use App\Services\Campaign\CampaignDeliveryService;
use App\Services\Campaign\CampaignEligibilityService;
use App\Services\Campaign\CampaignPushService;
use App\Services\Campaign\CartAbandonmentService;
use App\Classes\Settings;
use App\Events\Auth\PhoneVerified;
use App\Http\Middleware\DetectApplicationEnvironment;
use App\Listeners\Auth\ProcessReferralRewardListener;
use App\Listeners\PushNotificationFailedListener;
use App\Listeners\PushNotificationSentListener;
use App\Models\Old\RetailCustomer;
use App\Models\Old\User;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
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

        // Campaign services
        $this->app->singleton(CampaignConditionsService::class);
        $this->app->singleton(CampaignPushService::class);
        $this->app->singleton(CampaignEligibilityService::class);
        $this->app->singleton(CartAbandonmentService::class);
        $this->app->singleton(CampaignDeliveryService::class);
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->header('x-forwarded-proto') === 'https' 
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https' 
            || app()->environment(['production', 'staging']) 
            || str_starts_with(config('app.url'), 'https://')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

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

        \Event::listen(
            NotificationSent::class ,
            PushNotificationSentListener::class
        );

        // ── Referral System: award bonus when referred user verifies phone ──
        \Event::listen(
            PhoneVerified::class,
            ProcessReferralRewardListener::class
        );
    }
}
