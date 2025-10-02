<?php

namespace App\Classes;

use Spatie\Valuestore\Valuestore;

class Settings extends Valuestore
{

    public static $validation = [
        'store.name' => 'required|max:255',
        'store.first_address' => 'required',
        'store.contact_number' => 'required',
    ];

    /**
     * @param array $defaultSettings
     * @return void
     */
    public static function initSettings(array $defaultSettings) : void
    {
        app(Settings::class)->put($defaultSettings);
    }


    /**
     * @return Valuestore
     */
    public static function getSetting() : Valuestore
    {
        return app(Settings::class);
    }


    /**
     * @return array
     */
    public static function InProgress() : array
    {
        return [status('Processing Error'), status("Pending"), status("Processing"), status("Packing"), status("Waiting For Payment"), status("Paid")];
    }

    /**
     * @return array
     */
    public static function Completed() : array
    {
        return [status("Dispatched"), status("Completed")];
    }

    /**
     * @return array
     */
    public static function Cancelled() : array
    {
        return [status("Cancelled")];
    }

    /**
     * @param string $key
     * @return string|int|null
     */
    public final function uiSettings(string $key) : string|int|null
    {
        if(auth()->check()){
            if(isset(auth()->user()->{$key}) and !is_null((auth()->user()->{$key}))){
                return auth()->user()->{$key};
            }
        }

        return app(Settings::class)->get($key) ?? null;
    }

}
