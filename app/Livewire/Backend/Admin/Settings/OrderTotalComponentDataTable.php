<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\OrderTotal;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class OrderTotalComponentDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = OrderTotal::class;
    public static String $permissionComponentName = 'order_total';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.order_total.update',
            'destroy' => 'backend.admin.settings.order_total.destroy',
            'create'   => 'backend.admin.settings.order_total.create',
            'status' => 'backend.admin.settings.order_total.toggle',
        ];

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Other Total",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Order Total";

        $this->extraRowAction = [];

        $this->rowSpinner = [
            [
                'label' => 'Status',
                'field' => 'status'
            ]
        ];
    }


    public function builder(): Builder
    {
        return OrderTotal::query();
    }


    public function mount()
    {
        $this->modalName = "Order Total";

        $this->data = [
            'title' => ['label' => 'Title', 'type'=>'text'],
            'order_total_type' => ['label' => 'Type', 'type'=>'select', 'options' => collect([
                ['id'=> 'Flat', 'name' => 'Flat'],
                ['id'=> 'Percentage', 'name' => 'Percentage']
            ])->toArray()],
            'code' => ['label' => 'Code', 'type'=>'text'],
            'value' => ['label' => 'Amount', 'type'=>'text'],
        ];

        $this->newValidateRules = [
            'title' => 'required|min:3',
            'order_total_type' => 'required',
            'code' => 'required',
            'value' => 'required',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Title", "title")
                ->sortable(),
            Column::make("Type", "order_total_type")
                ->sortable(),
            Column::make("Amount", "value")
                ->sortable(),
            Column::make("Code", "code")
                ->sortable(),
        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.order_total')]
    public function view()
    {
    }
}
