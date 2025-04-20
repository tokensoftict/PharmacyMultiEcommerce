<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("customer_types")->insert([
            ['name' => "Pharmacy", 'status' => 1],
            ['name' => "Hospital", 'status' => 1],
            ['name' => "Clinic", 'status' => 1],
            ['name' => "Patent Medicine Store", 'status' => 1],
        ]);
    }
}
