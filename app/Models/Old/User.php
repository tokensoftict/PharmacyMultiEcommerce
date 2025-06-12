<?php

namespace App\Models\Old;


use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    protected $connection = 'old_server_mysql';

    protected $fillable = ['cus_exist', 'customer_group_id', 'type', 'firstname', 'lastname', 'username', 'device_key', 'cart', 'wishlist', 'address_id', 'status', 'cac_document', 'premises_licence', 'shop_name', 'ordertotals', 'coupon_data', 'checkout', 'email', 'email_verified_at', 'password', 'phone', 'email_verification_token', 'as_activated_before', 'remove_order_total', 'admin', 'remember_token', 'password_reset_token', 'created_at', 'updated_at', 'referral_agent_id', 'is_missing'];
    protected $appends = ['name'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getSalesRepAttribute(){
        return $this->isSalesRep();
    }
    public function getDriverAttribute(){
        return $this->isDriver();
    }
    public function isAdmin()
    {
        return $this->admin == 1; // this looks for an admin column in your users table 1
    }
    public function isSalesRep(){
        return $this->admin == 2; // this looks if the user is sales rep in user table 2
    }

    public function isDriver(){
        return $this->admin == 3; // this looks if the user is sales rep in user table 2
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'type');
    }
    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }


    public function addresses()
    {
        return $this->morphMany(Address::class, 'user');
    }


    public function orders()
    {
        return $this->morphMany(Order::class, 'user');
    }


    public function getNameAttribute(){
        return $this->attributes['firstname']." ".$this->attributes['lastname'];
    }

}


