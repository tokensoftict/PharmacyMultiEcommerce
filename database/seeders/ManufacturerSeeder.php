<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manufacturers = json_decode(file_get_contents(storage_path('app/manufacturer.json')), true);
        foreach ($manufacturers as $manu) {
            $manufacturer = Manufacturer::where('name', $manu['name'])->first();
            if (!Storage::disk('contabo')->exists("manufacturer/".$manu['image'])) continue;
            if($manufacturer) {
                $manufacturer->addMediaFromDisk("manufacturer/".$manu['image'], "contabo")
                    ->preservingOriginal()
                    ->toMediaCollection('medialibrary');
            }
        }
    }
}
