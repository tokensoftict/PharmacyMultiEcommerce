<?php

namespace App\Models\Old;

use Illuminate\Foundation\Auth\User as Authenticatable;


/**
 * @property integer $id
 * @property integer $customer_group_id
 * @property string $firstname
 * @property string $lastname
 * @property string $username
 * @property string $cart
 * @property string $wishlist
 * @property integer $address_id
 * @property integer $status
 * @property string $email
 * @property string $email_verified_at
 * @property string $password_reset_token
 * @property string $device_key
 * @property string $password
 * @property string $phone
 * @property integer $cus_exist
 * @property int $admin
 * @property string $checkout
 * @property string $ordertotals
 * @property string $remember_token
 * @property string $created_at
 * @property string $updated_at
 * @property string $mobile_verification
 * @property integer $code
 * @property CustomerGroup $customerGroup
 */
class RetailCustomer extends Authenticatable
{
    protected $connection = 'old_server_mysql';

    protected $table = 'retail_customer';
    protected $keyType = 'integer';

    protected $fillable = ['customer_group_id', 'firstname', 'coupon_data','type','lastname','remove_order_total' ,'username', 'cart', 'wishlist', 'address_id', 'status', 'email', 'email_verified_at', 'password_reset_token', 'device_key', 'password', 'phone', 'cus_exist', 'admin', 'checkout', 'ordertotals', 'code','remember_token', 'mobile_verification','created_at', 'updated_at', 'is_missing'];
    protected $appends = ['name'];

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function getNameAttribute(){
        return $this->firstname." ".$this->lastname;
    }

    public function getShopNameAttribute(){
        return $this->firstname;
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'user');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'type');
    }

}
