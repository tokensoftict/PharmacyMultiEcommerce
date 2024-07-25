<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

abstract class ExportDataTableComponent extends DataTableComponent
{
    public array $perPageAccepted = [20, 50, 100, 200, 500, 1000, 1500, 2000, -1];

    public function getExportBuilder() : Builder
    {

        $this->setupColumnSelect();
        $this->setupPagination();
        $this->setupSecondaryHeader();
        $this->setupFooter();
        $this->setupReordering();
        $this->baseQuery();
       return $this->getBuilder();
    }

}
