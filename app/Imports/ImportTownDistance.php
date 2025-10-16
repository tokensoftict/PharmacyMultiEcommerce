<?php

namespace App\Imports;

use App\DoorStepDeliveryTownDistance;
use App\Models\DeliveryTownDistance;
use App\Models\Town;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportTownDistance implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row)
        {
            if(empty($row['id'])) continue;
            if(!$town = Town::where('name', $row['town'])->first()) continue;
            if(empty($row['town_distance']) || empty($row['minimum_delivery_cost']) || empty($row['fixed_delivery_cost']) || empty($row['frequency'])) {
                continue;
            }
            $distance = [
                'town_distance' => empty($row['town_distance']) ? 0 : $row['town_distance'],
                'town_id' => $town->id,
                'minimum_shipping_amount' => empty($row['minimum_delivery_cost']) ? 0 : $row['minimum_delivery_cost'],
                'fixed_shipping_amount' => empty($row['fixed_delivery_cost']) ? 0 : $row['fixed_delivery_cost'],
                'delivery_days' =>empty($row['delivery_days']) ? NULL : explode(",",$row['delivery_days']),
                'no' => empty($row['no']) ? 1 : $row['no'],
                'frequency' => empty($row['frequency']) ? 'days' : $row['frequency']
            ];
            DeliveryTownDistance::updateOrCreate(['town_id'=>$row['id']], $distance);

        }
    }
}
