<?php

namespace App\Livewire\Backend\Admin\Stock;

use App\Classes\ExportDataTableComponent;
use App\Livewire\Backend\Component\StockRestrictionComponent;
use App\Models\CustomerType;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;


class StockRestrictionDataTable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = CustomerType::class;

    public static String $permissionComponentName = 'stock_restriction';


    public function builder(): Builder
    {
        return CustomerType::query()->withCount('group');
    }


    public function __construct()
    {
        $this->rowAction = [];

        $this->extraRowAction = ['upload_restriction_stock'];

        $this->actionPermission = [
            'upload_restriction_stock' => 'backend.admin.stock_manager.upload'
        ];

        $this->extraRowActionButton = [
            CustomerType::class =>  [
                'label' => 'Upload Stock',
                'type' => 'component',
                'route' => "backend.admin.stock_manager.upload",
                'permission' => 'upload_restriction_stock',
                'class' => 'btn btn-sm btn-outline-primary',
                'icon' => 'fa fa-cloud',
                'component' => StockRestrictionComponent::class,
                'is' => 'modal',
                'modal' => 'stock-restriction-component',
                'parameters' =>[]
            ]
        ];

        $this->pageHeaderTitle = "Stock Restrictions";

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Stock Restriction",
                'active' =>true
            ]
        ];


    }


    public function mount()
    {
        $this->modalName = "Stock Restriction";

        $this->data = [];

        $this->newValidateRules = [];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }

    public static function mountColumn() : array
    {
        return [
            Column::make("Customer Type", "name")
                ->sortable(),
            Column::make("No of Stock", "status")
               ->format(fn($value, $row, Column $column) => $row->group_count),
        ];
    }


}
