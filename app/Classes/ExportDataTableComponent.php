<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

abstract class ExportDataTableComponent extends DataTableComponent
{
    public array $perPageAccepted = [20, 50, 100, 200, 500, 1000, 1500, 2000, -1];

    public function getExportBuilder() : Builder
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $this->setupColumnSelect();
        $this->setupPagination();
        $this->setupReordering();
        $this->baseQuery();
        $this->setBuilder($this->selectFields());
        return $this->getBuilder();
    }

}
