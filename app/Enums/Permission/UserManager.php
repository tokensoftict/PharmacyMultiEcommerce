<?php

namespace App\Enums\Permission;

enum UserManager
{
    const CONFIG = [
        'name' => 'Administrator Manager',
        'label' => 'Administrator Manager',
        'description' => 'Manage Administrators',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2,3]
    ];

    const PERMISSION = [
        [
            "name" => "backend.admin.user.list",
            "label" => "Administrator List",
            "visibility" => "1",
            "guard_name" => "web",
            'app_id' => [2,3]
        ],
        [
            "name" => "backend.admin.user.create",
            "label" => "Add New Administrator",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.user.toggle",
            "label" => "Toggle",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.user.resend_invitation",
            "label" => "Re-send Invitation",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];
}
