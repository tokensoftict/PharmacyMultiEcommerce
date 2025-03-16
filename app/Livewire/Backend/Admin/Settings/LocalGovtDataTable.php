<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\LocalGovt;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class LocalGovtDataTable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = LocalGovt::class;
    public static String $permissionComponentName = 'local_government';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->extraRowAction = ['address_and_town'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.localgovt.update',
            'destroy' => 'backend.admin.settings.localgovt.destroy',
            'create'   => 'backend.admin.settings.localgovt.create',
            'status' => 'backend.admin.settings.localgovt.toggle',
            'address_and_town' => 'backend.admin.settings.address_and_town'
        ];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Local Governments",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Local Governments";

        $this->extraRowActionButton = [
            [
                'label' => 'View Towns',
                'type' => 'link',
                'route' => "backend.admin.settings.address_and_town",
                'permission' => 'address_and_town',
                'class' => 'btn btn-sm btn-outline-primary',
                'parameters' => [
                    'state'=>'state_id',
                    'localGovt' => 'id'
                ]
            ]
        ];

    }


    public function builder(): Builder
    {
        return LocalGovt::query()->with('state', 'towns', 'state.country');
    }


    public function mount()
    {
        $this->modalName = "Local Government";
        $this->data = [
            'state_id' => ['label' => 'State', 'type'=>'select',  'options'=> array_values(statesByCountry(config('app.DEFAULT_COUNTRY_ID'))->toArray()), 'placeholder' => "Select State"],
            'name' => ['label' => 'Local Government Name', 'type'=>'text'],
        ];

        $this->newValidateRules = [
            'state_id' => 'required',
            'name' => 'required|min:3',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("State", "state_id")
                ->format(function($value, $row, Column $column){
                    return $row->state->name;
                })
                ->sortable(),
            Column::make("Local Government Name", "name")
                ->sortable(),
        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.local_government')]
    public function view()
    {
    }
}
