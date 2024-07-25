<?php

namespace App\Exports;

use App\Exports\Sheets\StockRestrictionDummyDataSheet;
use App\Exports\Sheets\StockRestrictionGroupTypeSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StockRestrictionTemplate implements WithMultipleSheets
{

    private array $restrictionModuleConfig;

    public function __construct(array $restrictionModuleConfig)
    {
        $this->restrictionModuleConfig = $restrictionModuleConfig;
    }

    public function sheets(): array
    {
        return [
            new StockRestrictionDummyDataSheet(),
            new StockRestrictionGroupTypeSheet($this->restrictionModuleConfig)
        ];
    }


}
