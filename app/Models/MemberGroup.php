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
        'min_sales_amount' => 'float'
    ];

    protected $fillable = [
        'name',
        'label',
        'color',
        'bg_color',
        'min_sales_amount',
        'status'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'member_group_id');
    }
}
