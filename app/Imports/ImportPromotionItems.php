<?php

namespace App\Imports;

use App\Models\Promotion;
use App\Models\PromotionItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportPromotionItems implements ToCollection,WithHeadingRow
{
    private array $promotionItems = [];
    private Promotion $promotion;

    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row){
            $row = $row->toArray();
            if(empty($row['promo_price']) || $row['promo_price'] == "") continue;
            $this->promotionItems[] = new PromotionItem([
                'promotion_id' => $this->promotion->id,
                'stock_id' => $row['id'],
                'user_id' => $this->promotion->user_id,
                'status_id' => $this->promotion->status_id,
                'customer_group_id' => $this->promotion->customer_group_id,
                'customer_type_id' => $this->promotion->customer_type_id,
                'domain' => $this->promotion->domain,
                'from_date' => $this->promotion->from_date,
                'end_date' => $this->promotion->end_date,
                'created' => $this->promotion->created,
                'price' => $row['promo_price']
            ]);
        }
    }


    /**
     * @return array
     */
    public function getPromotionalItems() : array
    {
        return $this->promotionItems;

    }
}
