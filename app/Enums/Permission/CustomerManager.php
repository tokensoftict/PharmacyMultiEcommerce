<?php

namespace App\Enums\Permission;

enum CustomerManager
{
    const CONFIG = [
        'name' => 'Customer Manager',
        'label' => 'Customer Manager',
        'description' => 'Manage Customers',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2,3]
    ];

    const PERMISSION = [
        [
            "name" => "backend.admin.wholesales.customer_manager.list",
            "label" => "Customer List",
            "visibility" => "1",
            "guard_name" => "web",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.supermarket.customer_manager.list",
            "label" => "Customer List",
            "visibility" => "1",
            "guard_name" => "web",
            'app_id' => [3]
        ],
        [
            "name" => "backend.admin.customer_manager.wholesales.create",
            "label" => "Create Customer",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.customer_manager.wholesales.update",
            "label" => "Update Customer",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.customer_manager.wholesales.view",
            "label" => "View Customer",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.customer_manager.customer_search_history.list",
            "label" => "Customer Search History",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.customer_manager.customer_search_history.destroy.all",
            "label" => "Delete All Customer Search History",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];
}
