<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;

class PaymentMethodDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = PaymentMethod::class;
    public static String $permissionComponentName = 'payment_method';


    public function __construct()
    {
        $this->rowAction = [];

        $this->actionPermission = [
            'status' => 'backend.admin.settings.payment_methods.toggle',
            'payment_method_settings' => 'backend.admin.settings.payment_methods.settings'
        ];

        $this->extraRowAction = ['payment_method_settings'];

        $this->extraRowActionButton = [
            [
                'label' => 'Settings',
                'type' => 'link',
                'route' => "backend.admin.settings.payment_methods.settings",
                'permission' => 'payment_method_settings',
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
                'name' => "Payment Methods",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Payment Methods";


        $this->rowSpinner = [
            [
                'label' => 'Status',
                'field' => 'status'
            ]
        ];
    }


    public function mount()
    {
        $this->modalName = "Payment Methods";

        $this->data = [];

        $this->initControls();
    }

    public function builder(): Builder
    {
        return PaymentMethod::query()->where("domain", request()->getHost());
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
