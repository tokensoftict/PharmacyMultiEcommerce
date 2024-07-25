<?php

namespace App\Livewire\Backend\Admin\SalesRep;

use App\Classes\BooleanColumn;
use App\Classes\ExportDataTableComponent;
use App\Models\CustomerSearchHistory;
use App\Models\SalesRepresentative;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;


class SalesRepManagerDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = SalesRepresentative::class;

    public function __construct()
    {
        $this->extraRowAction = [];

        $this->toolbarButtons = [
            'pages/backend/admin/component/salesrep/createsalesrep' =>[
                'label' => 'New Sales Rep',
                'type' => 'component',
                'route' => "backend.admin.settings.sales_rep_manager.create",
                'permission' => 'create_new_sales_rep',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-plus',
                'component' => 'pages.backend.admin.component.salesrep.createsalesrep',
                'is' => 'modal',
                'modal' => 'pages.backend.admin.component.salesrep.createsalesrep',
                'parameters' =>[]
            ]
        ];

        $this->actionPermission = [
            'create_new_sales_rep'=> 'backend.admin.settings.sales_rep_manager.create'
        ];


        $this->pageHeaderTitle = "Sale Representatives List";

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Sale Representatives List",
                'active' =>true
            ]
        ];
    }

    public function mount()
    {
        $this->data = [];
    }

    public function builder(): Builder
    {
        return SalesRepresentative::query()->with(['user']);
    }

    /**
     * @return array
     */
    public static function  mountColumn() : array
    {
        return [
            Column::make("First Name", "user.firstname")
                ->searchable()
                ->sortable(),
            Column::make("Last Name", "user.lastname")
                ->searchable()
                ->sortable(),
            Column::make("Email Address", "user.email")
                ->searchable()
                ->sortable(),
            Column::make("Phone Number", "user.phone")
                ->searchable()
                ->sortable(),
            BooleanColumn::make("Invitation Status", "invitation_status")
                ->sortable(),
            Column::make("Invitation Date", "invitation_sent_date")
                ->format(function($value, $row, Column $column){
                    return $value  ? $value->format('l, F jS, Y') : "";
                })
                ->sortable(),
            Column::make("Accepted Date", "invitation_approval_date")
                ->format(function($value, $row, Column $column){
                    return $value  ? $value->format('l, F jS, Y') : "";
                })
                ->sortable(),
            BooleanColumn::make('Status', 'status')
            ->sortable()
        ];
    }
}
