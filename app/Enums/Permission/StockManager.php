<?php

namespace App\Enums\Permission;

enum StockManager
{
    const CONFIG = [
        'name' => 'Stock Manager',
        'label' => 'Stock Manager',
        'description' => 'Manage Stock Manager Module',
        'status' => '1',
        'visibility' => '1',
        'order' => '1',
        'icon' => 'icon',
        'app_id' => [2,3]
    ];


    const PERMISSION = [
        [
            "name" => "backend.admin.stock_manager.list_stock",
            "module_id" => "2",
            "label" => "List Stock",
            "visibility" => "1",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
        ],
        [
            "name" => "backend.admin.stock_manager.stock_restriction",
            "module_id" => "2",
            "label" => "Stock Restriction",
            "visibility" => "1",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.stock_manager.stock_restriction.upload",
            "module_id" => "2",
            "label" => "Upload Stock Restriction",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.stock_manager.stock_restriction.view_stocks",
            "module_id" => "2",
            "label" => "View Stocks in Stock Restriction",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.stock_manager.stock_size",
            "module_id" => "2",
            "label" => "Stock Size",
            "visibility" => "1",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.stock_manager.stock_size.update",
            "module_id" => "2",
            "label" => "Update Stock Size",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.stock_manager.stock_size.destroy",
            "module_id" => "2",
            "label" => "Destroy Stock Size",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.stock_manager.stock_size.create",
            "module_id" => "2",
            "label" => "Create Stock Size",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.stock_manager.stock_size.upload",
            "module_id" => "2",
            "label" => "Upload Stock Size",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
            'app_id' => [2]
        ],
        [
            "name" => "backend.admin.stock_manager.set_featured",
            "module_id" => "2",
            "label" => "Set Stock as Featured",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
        ],
        [
            "name" => "backend.admin.stock_manager.special_offer",
            "module_id" => "2",
            "label" => "Set Stock as Special Offer",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
        ],
        [
            "name" => "backend.admin.stock_manager.admin_status",
            "module_id" => "2",
            "label" => "Enable / Disable Stock Online",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
        ],
        [
            "name" => "backend.admin.stock_manager.view",
            "module_id" => "2",
            "label" => "View Stock",
            "visibility" => "0",
            "guard_name" => "web",
            "created_at" => "2024-03-26 11:54:16",
            "updated_at" => "2024-03-26 11:54:16",
        ],
    ];

}
