<?php

namespace App\Enums\Permission;

enum FeedBackDashboard
{
    const CONFIG = [
        'name' => 'Feedback Dashboard',
        'label' => 'Feedback Dashboard',
        'description' => 'Feedback Dashboard',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2, 3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.feedback.dashboard",
            "label" => "Feedback Dashboard",
            "visibility" => "1",
            "guard_name" => "web",
            'app_id' => [2, 3]
        ],
        [
            "name" => "backend.admin.feedback.list",
            "label" => "Feedback List",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.feedback.staff-reports",
            "label" => "Staff Performance Reports",
            "visibility" => "1",
            "guard_name" => "web",
            'app_id' => [2, 3]
        ],
    ];

}
