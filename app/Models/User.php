<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Classes\Password\CanResetPasswordByMobile;
use App\Classes\Password\CanResetPasswordByTokenInterface;
use App\Traits\MustVerifyPhone;
use App\Traits\UserModelTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class User
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $phone_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string|null $verification_pin
 * @property string|null $email_verification_pin
 * @property string|null $verification_token
 * @property string|null $image
 * @property string $theme
 * @property string $navigation_type
 * @property Carbon|null $last_seen
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|App[] $apps
 * @property Collection|Coupon[] $coupons
 * @property Collection|PromotionItem[] $promotion_items
 * @property Collection|Promotion[] $promotions
 * @property Collection|PushNotification[] $push_notifications
 * @property Collection|SalesRepresentative[] $sales_representatives
 * @property Collection|StockRestriction[] $stock_restrictions
 * @property Collection|Stock[] $stocks
 * @property Collection|SupermarketUser[] $supermarket_users
 * @property Collection|VoucherCode[] $voucher_codes
 * @property Collection|Voucher[] $vouchers
 * @property Collection|WholesalesUser[] $wholesales_users
 *
 * @package App\Models
 */

class User extends Authenticatable implements CanResetPasswordByTokenInterface, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, UserModelTrait, CanResetPasswordByMobile, MustVerifyPhone, SoftDeletes;

    protected $table = 'users';

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'last_seen' => 'datetime'
    ];

    protected $hidden = [
        'password',
        'verification_token',
        'remember_token',
        'auth_code'
    ];

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'email_verified_at',
        'phone_verified_at',
        'password',
        'phone',
        'verification_pin',
        'email_verification_pin',
        'verification_token',
        'image',
        'theme',
        'navigation_type',
        'last_seen',
        'remember_token',
        'auth_code'
    ];

    protected $appends =  ['cus_exist'];

    public function getCusExistAttribute()
    {
        return "";
    }

    public final function getNameAttribute()
    {
        return ucwords($this->firstname)." ".ucwords($this->lastname);
    }

    public function apps()
    {
        return $this->belongsToMany(App::class, 'app_users')
            ->withPivot('id', 'domain', 'user_type_type', 'user_type_id')
            ->withTimestamps();
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'created_by');
    }

    public function promotion_items()
    {
        return $this->hasMany(PromotionItem::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function push_notifications()
    {
        return $this->hasMany(PushNotification::class);
    }

    public function sales_representatives()
    {
        return $this->hasMany(SalesRepresentative::class);
    }

    public function stock_restrictions()
    {
        return $this->hasMany(StockRestriction::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function supermarket_users()
    {
        return $this->hasMany(SupermarketUser::class);
    }

    public function voucher_codes()
    {
        return $this->hasMany(VoucherCode::class, 'created_by');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'created_by');
    }

    public function wholesales_users()
    {
        return $this->hasMany(WholesalesUser::class);
    }

    public function wholesales_admins()
    {
        return $this->hasMany(WholesalesAdmin::class);
    }

    public function supermarket_admins()
    {
        return $this->hasMany(SupermarketAdmin::class);
    }

    public function app_users()
    {
        return $this->hasMany(AppUser::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
