<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wholesalesAppId = 5;
        $supermarketAppId = 4;
        $statusActive = 1;
        $userId = 1; // Assuming admin user id 1 exists

        // Create Wholesales Promo
        $promoW = \App\Models\Promotion::create([
            'name' => 'Wholesales Mega Sale',
            'user_id' => $userId,
            'status_id' => $statusActive,
            'app_id' => $wholesalesAppId,
            'from_date' => now()->subDay(),
            'end_date' => now()->addDays(3),
            'created' => now(),
        ]);

        // Link some stocks to Wholesales Promo
        foreach ([1, 2, 3] as $stockId) {
            \App\Models\PromotionItem::create([
                'promotion_id' => $promoW->id,
                'stock_id' => $stockId,
                'user_id' => $userId,
                'status_id' => $statusActive,
                'app_id' => $wholesalesAppId,
                'from_date' => $promoW->from_date,
                'end_date' => $promoW->end_date,
                'created' => now(),
                'price' => 100.00, // Sample promo price
            ]);
        }

        // Create Supermarket Promo
        $promoS = \App\Models\Promotion::create([
            'name' => 'Easter Supermarket Bonanza',
            'user_id' => $userId,
            'status_id' => $statusActive,
            'app_id' => $supermarketAppId,
            'from_date' => now()->subDay(),
            'end_date' => now()->addDays(5),
            'created' => now(),
        ]);

        // Link some stocks to Supermarket Promo
        foreach ([4, 5] as $stockId) {
            \App\Models\PromotionItem::create([
                'promotion_id' => $promoS->id,
                'stock_id' => $stockId,
                'user_id' => $userId,
                'status_id' => $statusActive,
                'app_id' => $supermarketAppId,
                'from_date' => $promoS->from_date,
                'end_date' => $promoS->end_date,
                'created' => now(),
                'price' => 50.00, // Sample promo price
            ]);
        }
    }
}
