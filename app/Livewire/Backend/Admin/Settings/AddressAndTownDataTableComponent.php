<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\LocalGovt;
use App\Models\State;
use App\Models\Town;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AddressAndTownDataTableComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = Town::class;

    public State $state;

    public LocalGovt $localGovt;

    public static String $permissionComponentName = 'address_and_town';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.address_and_town.update',
            'destroy' => 'backend.admin.settings.address_and_town.destroy',
            'create'   => 'backend.admin.settings.address_and_town.create',
        ];

        $this->extraRowAction = [];

        $this->pageHeaderTitle = "Address and Town";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'backend.admin.settings.local_government'),
                'name' => "Local Governments",
                'active' =>false
            ],
            [
                'name' => "Address and Town",
                'active' =>true
            ]
        ];


    }

    public function builder(): Builder
    {
        return Town::where('state_id', $this->state->id)->where('local_govt_id', $this->localGovt->id)->with(['state', 'local_govt']);
    }


    public function mount()
    {
        $this->modalName = "Address And Town";

        $this->data = [
            'state_id' => ['label' => 'State', 'type'=>'hidden', 'display' => $this->state->name, 'value' => $this->state->id],
            'local_govt_id' => ['label' => 'Local Govt', 'type'=>'hidden', 'display' => $this->localGovt->name, 'value' => $this->localGovt->id],
            'name' => ['label' => 'Town Name', 'type'=>'text'],
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
            Column::make("State", "state_id")
                ->format(function($value, $row, Column $column){
                    return $row->state->name;
                })
                ->sortable(),
            Column::make("Local Govt", "local_govt_id")
                ->format(function($value, $row, Column $column){
                    return $row->local_govt->name;
                })
                ->sortable(),
            Column::make("Town Name", "name")
                ->sortable(),
        ];
    }

    #[PermissionAttribute('Show', 'show', 'backend.admin.settings.address_and_town')]
    public function show()
    {
    }

}
