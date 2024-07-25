<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use App\Models\Productgroup;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class CustomerTypeComponentDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = CustomerType::class;
    public static String $permissionComponentName = 'customer_type';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.customer_type.update',
            'destroy' => 'backend.admin.settings.customer_type.destroy',
            'create'   => 'backend.admin.settings.customer_type.create',
            'status' => 'backend.admin.settings.customer_type.toggle',
        ];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Customer Types",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Customer Types";

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
        return CustomerType::query();
    }


    public function mount()
    {
        $this->modalName = "Customer Type";

        $this->data = [
            'name' => ['label' => 'Name', 'type'=>'text'],
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Name", "name")
                ->sortable(),
        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.customer_type')]
    public function view()
    {
    }
}
