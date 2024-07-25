<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\CustomerGroup;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class CustomerGroupComponentDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = CustomerGroup::class;
    public static String $permissionComponentName = 'customer_group';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.customer_group.update',
            'destroy' => 'backend.admin.settings.customer_group.destroy',
            'create'   => 'backend.admin.settings.customer_group.create',
            'status' => 'backend.admin.settings.customer_group.toggle',
        ];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Customer Groups",
                'active' =>true
            ]
        ];

        $this->pageHeaderTitle = "Customer Groups";

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
        return CustomerGroup::query();
    }


    public function mount()
    {
        $this->modalName = "Customer Group";

        $this->data = [
            'name' => ['label' => 'Name', 'type'=>'text'],
            'description' => ['label' => 'Description', 'type'=>'textarea'],
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
            Column::make("Description", "description")

        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.customer_group')]
    public function view()
    {
    }
}
