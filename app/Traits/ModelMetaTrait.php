<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait ModelMetaTrait
{
    public static function meta($data,$extra = []) : Collection
    {
        return   collect(
            array_merge(
                [
                    'from'  => request()->from ?: 1,
                    'to'    => request()->to ?: 50,
                    'total' => parent::query()->count(),
                    'count' => $data->count(),
                ],
                $extra
            )
        );

    }

    public  function scopepaginateRecord($query) : Builder
    {
        return $query->latest()->offset((request()->from ?: 1) - 1)->limit((request()->to ?: 50));
    }

    public function scopemountWise($query, $date_column, $Apiresource)
    {
        return $Apiresource::collection($query->get())->groupBy(function($item) use ($date_column){
            return Carbon::parse($item->{$date_column})->format('F');
        });
    }
}
