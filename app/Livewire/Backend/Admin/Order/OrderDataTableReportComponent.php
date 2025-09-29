<?php

namespace App\Livewire\Backend\Admin\Order;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Models\Order;
use App\Models\WholesalesUser;
use App\Models\WholessalesStockPrice;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class OrderDataTableReportComponent extends OrderDataTableComponent
{

    public function __construct()
    {
        $request = request()->all();
        if(!isset($request['startDate']) and !isset($request['stopDate']) == 0) {
            $this->filter = ['startDate' => date('Y-m-d'), 'stopDate' => date('Y-m-d')];
        } else {
            $this->filter = $request;
        }

        parent::__construct();

        $this->pageHeaderTitle  = "Orders Report";

        $this->filterResetLink = route(ApplicationEnvironment::$storePrefix.'backend.admin.order.report');

        $this->filterTable      = \View::make('filter.order', ['filterResetLink' => $this->filterResetLink])->render();
    }

}

