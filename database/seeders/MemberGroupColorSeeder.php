<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberGroupColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            'GOLD' => [
                'start' => '#FFD700',
                'end' => '#B8860B',
            ],
            'SILVER' => [
                'start' => '#C0C0C0',
                'end' => '#708090',
            ],
            'BRONZE' => [
                'start' => '#A85E34',
                'end' => '#7A3E1D',
            ],
        ];

        foreach ($groups as $name => $colors) {
            \App\Models\MemberGroup::where('name', $name)->update([
                'card_gradient_start' => $colors['start'],
                'card_gradient_end' => $colors['end'],
            ]);
        }
    }
}
