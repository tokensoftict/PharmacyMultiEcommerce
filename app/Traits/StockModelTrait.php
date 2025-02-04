<?php
namespace App\Traits;

use App\Classes\ApplicationEnvironment;
use Illuminate\Database\Eloquent\Builder;

trait StockModelTrait
{

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        static::addGlobalScope("filter_stocks", function (Builder $builder){
            $builder->with([ApplicationEnvironment::$stock_model_string])
                ->whereHas(ApplicationEnvironment::$stock_model_string);
        });

    }

    public final function getProductImageAttribute() : string
    {
        if(!is_null($this->image)) return asset($this->image);

        return asset("logo/placholder.jpg");
    }
}
