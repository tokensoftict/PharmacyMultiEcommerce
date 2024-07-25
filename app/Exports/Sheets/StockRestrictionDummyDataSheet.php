<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class StockRestrictionDummyDataSheet implements FromCollection, WithHeadings, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(
            [
                [
                    'Local Stock ID' => '1',
                    'name' => 'Sample Stock 1'
                ],
                [
                    'Local Stock ID' => '2',
                    'name' => 'Sample Stock 3'
                ],
                [
                    'Local Stock ID' => '3',
                    'name' => 'Sample Stock 3'
                ],
                [
                    'Local Stock ID' => '4',
                    'name' => 'Sample Stock 4'
                ],
                [
                    'Local Stock ID' => '5',
                    'name' => 'Sample Stock 5'
                ]

            ]
        );
    }

    public function headings(): array
    {
        return [
            'Local Stock ID',
            'Name'
        ];
    }

    public function title(): string
    {
        return 'Stocks';
    }
}
