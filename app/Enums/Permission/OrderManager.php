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
        'app_id' => [2,3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.order.list",
            "label" => "Orders",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.order.report",
            "label" => "Order Reports",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.order.view",
            "label" => "View Order",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.order.update",
            "label" => "Edit Order",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.order.update_product",
            "label" => "Edit Order Product",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];

}
