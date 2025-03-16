<?php

namespace App\Enums\Permission;

enum SalesRepManager
{
    const CONFIG = [
        'name' => 'Sales Rep Manager',
        'label' => 'Sales Rep Manager',
        'description' => 'Sales Rep Manager',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2]
    ];

    const PERMISSION = [
        [
            "name" => "backend.admin.sales_rep_manager.list",
            "label" => "Sales Rep List",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.sales_rep_manager.create",
            "label" => "New Sales Rep",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.sales_rep_manager.update",
            "label" => "Update Sales Rep",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.sales_rep_manager.view_report",
            "label" => "View Sales Rep Report",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.sales_rep_manager.toggle",
            "label" => "Disable / Enable Sales Rep",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];
}
