<?php

namespace App\Enums\Permission;

enum OrderManager
{
    const CONFIG = [
        'name' => 'Sales Order Manager',
        'label' => 'Sales Order Manager',
        'description' => 'Manage Sales Order Manager Module',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.order.list",
            "label" => "Orders",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.coupon.view",
            "label" => "View Order",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];

}
