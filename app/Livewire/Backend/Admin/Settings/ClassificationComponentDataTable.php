<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Classification;

class ClassificationComponentDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = Classification::class;
    public static String $permissionComponentName = 'classification';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Classifications",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Classifications";

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.classification.update',
            'destroy' => 'backend.admin.settings.classification.destroy',
            'create'   => 'backend.admin.settings.classification.create',
            'status' => 'backend.admin.settings.classification.toggle',
        ];

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
        return Classification::query();
    }


    public function mount()
    {
        $this->modalName = "Classification";

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

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.classification')]
    public function view()
    {
    }
}
