<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponStock extends Model
{
    protected $fillable = [
        'coupon_id',
        'local_stock_id'
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
