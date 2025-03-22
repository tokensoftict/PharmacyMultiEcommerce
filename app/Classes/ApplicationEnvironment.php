<?php

namespace App\Classes;

use App\Models\App;
use App\Models\AppUser;
use App\Models\SupermarketsStockPrice;
use App\Models\SupermarketUser;
use App\Models\WholesalesUser;
use App\Models\WholessalesStockPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PhpParser\Node\NullableType;


class ApplicationEnvironment
{

    public static App $application;

    public static string|null $name,$description, $logo, $domain, $link, $type, $id, $model_id;
    public static string $appModel, $appRelated;
    public static string $stock_model, $stock_model_string;
    public static string $frontEndAppName;

    public static function createApplicationEnvironment(App $application) : void
    {
        self::$name = $application->name;
        self::$description = $application->description;
        self::$logo = $application->logo;
        self::$domain = $application->domain;
        self::$link = $application->link;
        self::$type = $application->type;
        self::$id = $application->id;
        self::$model_id = $application->model_id;
        self::$stock_model = match ($application->model_id){
            6 => SupermarketsStockPrice::class,
            default=>WholessalesStockPrice::class,
        };
        self::$stock_model_string = match ($application->model_id){
            6 => "supermarkets_stock_prices",
            default=>"wholessales_stock_prices",
        };
        self::$frontEndAppName = App::where('model_id', $application->model_id)->where('type', 'Frontend')->first()->name;
        $appUser = AppUser::where("app_id", $application->id)->first();

        if($appUser){
            self::$appModel = $appUser->user_type_type;
            self::$appRelated =  Str::snake(
                (new \ReflectionClass($appUser->user_type_type))->getShortName()
            );
        }

        if(str_contains(self::$domain, "auth")) {
            self::$appModel = SupermarketUser::class;
            self::$appRelated =  Str::snake(
                (new \ReflectionClass(SupermarketUser::class))->getShortName()
            );
        }

    }


    /**
     * @return WholesalesUser|SupermarketUser|bool
     */
    public static function getApplicationRelatedModel() : WholesalesUser | SupermarketUser | bool
    {
        $user = request()->user();
        $applicationModel = self::$appRelated;
        if($applicationModel == "user") $applicationModel = "supermarket_user";
        return $user?->$applicationModel()?->first();
    }

    /**
     * @return bool
     */
    public static function isBackEndEnvironment() : bool
    {
        return self::$type === "Backend";
    }


    /**
     * @return bool
     */
    public static function isFrontEndEnvironment() : bool
    {
        return self::$type === "Frontend";
    }
}
