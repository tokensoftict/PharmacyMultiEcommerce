<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderTotalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('order_totals')->insert([
            [
                'title' => "Send SMS When Order is Ready",
                'order_total_type' => "Flat",
                'code' => "SMSS",
                'status' => '0',
                'value' => '15.00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => "Send SMS when my order is ready for dispatch",
                'order_total_type' => "Flat",
                'code' => "SMSSS",
                'status' => '0',
                'value' => '5.00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => "Notify me by SMS when my order is ready for Dispatch",
                'order_total_type' => "Flat",
                'code' => "SMSSSS",
                'status' => '0',
                'value' => '20.00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
