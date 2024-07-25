<?php

namespace App\Exports;

use App\Models\DeliveryTownDistance;
use App\Models\Town;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportTownDistance implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'ID',
            'Town',
            'State',
            'Town Distance',
            'Minimum Delivery Cost',
            'Fixed Delivery Cost',
            'Delivery Days',
            'No',
            'Frequency',
        ];
    }



    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $towns = Town::with(['local_govt','state'])->get();
        $export_town = [];
        foreach ($towns as $town)
        {
            $distance = DeliveryTownDistance::where('town_id',$town->id)->first();
            $export_town[] = [
                'id' => $town->id,
                'Town' => $town->name,
                'State' => $town->state->name,
                'Town Distance' => ($distance ? $distance->town_distance : ""),
                'Minimum Delivery Cost' => ($distance ? $distance->minimum_shipping_amount : ""),
                'Fixed Delivery Cost' =>  ($distance ? $distance->fixed_shipping_amount : ""),
                'Delivery Days' =>  ($distance ? implode(",",$distance->delivery_days) : ""),
                'No' =>($distance ? $distance->no : 1),
                'Frequency' => ($distance ? $distance->frequency : ""),
            ];
        }

        return collect($export_town);
    }
}
