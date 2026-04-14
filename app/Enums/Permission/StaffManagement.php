<?php

namespace App\Enums\Permission;

enum StaffManagement
{
    const CONFIG = [
        'name' => 'Staff Management',
        'label' => 'Staff Management',
        'description' => 'Manage Staff on Feedback System',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2,3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.staff.list",
            "label" => "Administrator List",
            "visibility" => "1",
            "guard_name" => "web",
            'app_id' => [2,3]
        ],
        [
            "name" => "backend.admin.staff.create",
            "label" => "Add New Administrator",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.staff.toggle",
            "label" => "Toggle",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];

}
