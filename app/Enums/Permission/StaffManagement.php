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
        'app_id' => [2, 3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.staff.list",
            "label" => "Staff List",
            "visibility" => "1",
            "guard_name" => "web",
            'app_id' => [2, 3]
        ],
        [
            "name" => "backend.admin.staff.create",
            "label" => "Add New Staff",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.staff.update",
            "label" => "Update Med Reminder",
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
