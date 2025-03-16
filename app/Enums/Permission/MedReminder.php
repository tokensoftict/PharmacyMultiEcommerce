<?php

namespace App\Enums\Permission;

enum MedReminder
{
    const CONFIG = [
        'name' => 'MedReminder',
        'label' => 'Med Reminder',
        'description' => 'MedReminder Module',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.med_reminder.list",
            "label" => "Med Reminder List",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.med_reminder.create",
            "label" => "Create Med Reminder",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.med_reminder.update",
            "label" => "Update Med Reminder",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.med_reminder.destroy",
            "label" => "Delete Med Reminder",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.med_reminder.view_report",
            "label" => "View Med Reminder Schedules",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.med_reminder.push",
            "label" => "Re-Push Med Reminder To users Phone",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];

}
