<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("customer_groups")->insert([
            ['name' => "BRONZE", 'status' => 1],
            ['name' => "SILVER", 'status' => 1],
        ]);
    }
}
