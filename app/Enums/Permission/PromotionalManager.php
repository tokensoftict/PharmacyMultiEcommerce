<?php

namespace App\Enums\Permission;

enum PromotionalManager
{
    const CONFIG = [
        'name' => 'Promotion Manager',
        'label' => 'Promotion Manager',
        'description' => 'Manage Promotion Manager Module',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2,3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.promotion.list",
            "label" => "Promotions",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.promotion.create",
            "label" => "Create Promotion",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.promotion.update",
            "label" => "Update Promotion",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.promotion.destroy",
            "label" => "Delete Promotion",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.promotion.approve",
            "label" => "Approve Promotion",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.promotion.decline",
            "label" => "Decline Promotion",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.promotion.view_report",
            "label" => "View Promotions Report",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];

}
