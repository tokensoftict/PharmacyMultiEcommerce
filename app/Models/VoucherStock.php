<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherStock extends Model
{
    protected $fillable = [
        'voucher_id',
        'local_stock_id',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
