<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Models\DeliveryMethod;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;

class DeliveryMethodDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = DeliveryMethod::class;
    public static String $permissionComponentName = 'delivery_method';


    public function __construct()
    {
        $this->rowAction = [];

        $this->actionPermission = [
            'status' => 'backend.admin.settings.delivery_methods.toggle',
            'delivery_methods_settings' => 'backend.admin.settings.delivery_methods.settings'
        ];

        $this->extraRowAction = ['delivery_methods_settings'];

        $this->extraRowActionButton = [
            [
                'label' => 'Settings',
                'type' => 'link',
                'route' => "backend.admin.settings.delivery_methods.settings",
                'permission' => 'delivery_methods_settings',
                'class' => 'btn btn-sm btn-outline-primary',
                'icon' => 'fa fa-gear',
            ]
        ];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Delivery Methods",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Delivery Methods";


        $this->rowSpinner = [
            [
                'label' => 'Status',
                'field' => 'status'
            ]
        ];
    }


    public function mount()
    {
        $this->modalName = "Delivery Methods";

        $this->data = [];

        $this->initControls();
    }

    public function builder(): Builder
    {
        return DeliveryMethod::query()->where("app_id", ApplicationEnvironment::$model_id);
    }



    public static function  mountColumn() : array
    {
        return [
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Code", "code")
                ->sortable(),
        ];
    }
}
