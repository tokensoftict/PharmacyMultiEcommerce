<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class MemberGroup
 * 
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string|null $color
 * @property string|null $bg_color
 * @property float $min_sales_amount
 * @property bool $status
 */
class MemberGroup extends Model
{
    protected $table = 'member_groups';

    protected $casts = [
        'status' => 'bool',
        'min_sales_amount' => 'float',
        'retail_min_sales_amount' => 'float',
        'member_discount' => 'float',
        'discount_until' => 'date'
    ];

    protected $fillable = [
        'name',
        'label',
        'color',
        'bg_color',
        'min_sales_amount',
        'retail_color',
        'retail_bg_color',
        'retail_min_sales_amount',
        'member_discount',
        'discount_until',
        'card_gradient_start',
        'card_gradient_end',
        'status'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'member_group_id');
    }
}
