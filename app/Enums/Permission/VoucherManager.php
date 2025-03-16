<?php

namespace App\Enums\Permission;

enum VoucherManager
{
    const CONFIG = [
        'name' => 'Voucher Manager',
        'label' => 'Voucher Manager',
        'description' => 'Manage Voucher Manager Module',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2,3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.voucher.list",
            "label" => "Vouchers",
            "visibility" => "1",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.voucher.create",
            "label" => "Create Voucher",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.voucher.update",
            "label" => "Update Voucher",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.voucher.destroy",
            "label" => "Delete Voucher",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.voucher.approve",
            "label" => "Approve Voucher",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.voucher.decline",
            "label" => "Decline Voucher",
            "visibility" => "0",
            "guard_name" => "web",
        ],
        [
            "name" => "backend.admin.voucher.view_report",
            "label" => "View Voucher Report",
            "visibility" => "0",
            "guard_name" => "web",
        ],
    ];

}
