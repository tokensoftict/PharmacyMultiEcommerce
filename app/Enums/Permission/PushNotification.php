<?php

namespace App\Enums\Permission;

enum PushNotification
{
    const CONFIG = [
        'name' => 'Push Notification',
        'label' => 'Push Notification',
        'description' => 'Manage Push Notifications',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2,3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.push_notification.list",
            "label" => "List Push Notification",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.push_notification.create",
            "label" => "Create New Push Notification",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.push_notification.update",
            "label" => "Update Push Notification",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.push_notification.approve",
            "label" => "Approve Push Notification",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.push_notification.cancel",
            "label" => "Cancel Push Notification",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.push_notification.view",
            "label" => "View Notification",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];
}
