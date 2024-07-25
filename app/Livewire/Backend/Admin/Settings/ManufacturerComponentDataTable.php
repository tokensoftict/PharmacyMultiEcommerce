<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Builder;

class ManufacturerComponentDataTable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = Manufacturer::class;
    public static String $permissionComponentName = 'manufacturer';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.manufacturer.update',
            'destroy' => 'backend.admin.settings.manufacturer.destroy',
            'create'   => 'backend.admin.settings.manufacturer.create',
            'status' => 'backend.admin.settings.manufacturer.toggle',
        ];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Manufacturers",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Manufacturers";

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
        return Manufacturer::query();
    }


    public function mount()
    {
        $this->modalName = "Manufacturer";

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
                ->sortable(),
        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.manufacturer')]
    public function view()
    {
    }
}
