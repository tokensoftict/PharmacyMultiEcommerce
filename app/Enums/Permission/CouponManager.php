<?php

namespace App\Enums\Permission;

enum CouponManager
{
    const CONFIG = [
        'name' => 'Coupon Manager',
        'label' => 'Coupon Manager',
        'description' => 'Manage Coupons Manager Module',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.coupon.list",
            "label" => "Coupons",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.coupon.create",
            "label" => "Create Coupon",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.coupon.update",
            "label" => "Update Coupon",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.coupon.destroy",
            "label" => "Delete Coupon",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.coupon.approve",
            "label" => "Approve Coupon",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.coupon.decline",
            "label" => "Decline Coupon",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.coupon.view_report",
            "label" => "View Coupon Report",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];

}
