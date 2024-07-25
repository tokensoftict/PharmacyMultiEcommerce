<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class StockRestrictionGroupTypeSheet implements FromCollection, WithHeadings, WithTitle
{

    protected array $restrictionModuleConfig;

    public function __construct(array $restrictionModuleConfig)
    {
        $this->restrictionModuleConfig = $restrictionModuleConfig;
    }


    public function collection()
    {
        return collect(
            [
                [
                    'group_id' => $this->restrictionModuleConfig['group_id'],
                    'group_type' => $this->restrictionModuleConfig['group_type']
                ]
            ]
        );
    }

    public function headings(): array
    {
        return [
            'group_id',
            'group_type'
        ];
    }

    public function title(): string
    {
        return 'Restriction Config';
    }
}
