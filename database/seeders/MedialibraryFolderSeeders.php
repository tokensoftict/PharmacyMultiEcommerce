<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use LivewireFilemanager\Filemanager\Models\Folder;

class MedialibraryFolderSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Folder::create([
           'parent_id' => NULL,
           'name' => "PSGDC",
           "slug" => "psgdc",
           "created_at" => now(),
           "updated_at" => now()
       ]);

        Folder::create([
            'parent_id' => 1,
            'name' => "stocks",
            "slug" => "stocks",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        Folder::create([
            'parent_id' => 1,
            'name' => "image slider",
            "slug" => "image-slider",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        Folder::create([
            'parent_id' => 1,
            'name' => "Business Certificate",
            "slug" => "business-certificate",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        Folder::create([
            'parent_id' => 1,
            'name' => "Business Premises License",
            "slug" => "business-premises-license",
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
