<?php

namespace Database\Seeders;

use App\Classes\Settings;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::initSettings(
            [
                'navigation_type' => 'default',
                'theme' => 'light',
                'lang'=> 'en-US',
                'dir' => 'ltr',
                'horizontal_shape' => 'default',
                'title' => 'Tokensoft Phoenix Livewire Starter Kit'
            ]
        );
    }
}
