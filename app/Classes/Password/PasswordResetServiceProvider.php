<?php

namespace App\Classes\Password;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider as BasePasswordResetServiceProvider;


class PasswordResetServiceProvider extends BasePasswordResetServiceProvider
{

    /**
     * @return void
     */
    protected function registerPasswordBroker()
    {
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });

    }

}
