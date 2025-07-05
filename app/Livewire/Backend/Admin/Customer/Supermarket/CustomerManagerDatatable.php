<?php

namespace App\Livewire\Backend\Admin\Customer\Supermarket;

use App\Classes\ApplicationEnvironment;
use App\Livewire\Backend\Component\WholeSales\WholeSalesCustomerFormComponent;
use App\Models\Address;
use App\Models\AppUser;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use App\Models\SupermarketUser;
use App\Models\WholesalesUser;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CustomerManagerDatatable extends DataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = SupermarketUser::class;

    public static String $permissionComponentName = 'supermarket_customer_manager';

    public function __construct()
    {
        $this->rowAction = ['edit'];

        $this->actionPermission = [
            'edit' => 'backend.admin.customer_manager.wholesales.update',
            'view' => 'backend.admin.customer_manager.wholesales.view',
        ];
//backend.admin.sales_rep_manager.view_report
        $this->extraRowAction = ['view'];

        $this->extraRowActionButton = [
            [
                'label' => 'View',
                'type' => 'link',
                'route' => "backend.admin.customer_manager.wholesales.view",
                'permission' => 'view',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-eye-o',
            ]
        ];


        $this->toolbarButtons = [
        ];

        $this->pageHeaderTitle = "Customer Lists";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Customer Lists",
                'active' =>true
            ]
        ];

    }

    public function builder(): Builder
    {
        return SupermarketUser::query()->with(['customer_group', 'user', 'customer_type'])->orderBy('id', 'desc');
    }

    public function mount()
    {
        $this->modalName = "Customer";

        $this->data = [
            'customer_type_id' => ['label' => 'Customer Type', 'type' => 'select', 'options' => CustomerType::select('id', 'name')->where('status', 1)->get()->toArray()],
            'customer_group_id' => ['label' => 'Customer Group', 'type' => 'select', 'options' => CustomerGroup::select('id', 'name')->where('status', 1)->get()->toArray()],
            'phone' => ['label' => 'Phone Number', 'type'=>'text'],
        ];

        $this->newValidateRules = [
            'phone' => 'required',
        ];
        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }

    public static function  mountColumn() : array
    {
        return [
            Column::make("First Name", "user.firstname")->sortable(),
            Column::make("Last Name", "user.lastname")->sortable(),
            Column::make("Email", "user.email")->sortable(),
            Column::make("Type", "customer_type.name")
                ->format(fn($value, $row, Column $column) => $value)
                ->sortable(),
            Column::make("Group", "customer_group.name")
                ->format(fn($value, $row, Column $column) => $value)
                ->sortable(),
            Column::make("Phone Number", "phone")->sortable(),
            Column::make("Exist On Local", "customer_local_id")
                ->format(function($value, $row, Column $column){
                    return match ($value){
                        null => '<span class="badge text-bg-danger">No</span>',
                        !null => '<span class="badge text-bg-success">Yes</span>'
                    };
                })->sortable()->html()
                ->sortable(),
            Column::make("Status", "status")
                ->format(function($value, $row, Column $column){
                    return [
                        '1' => '<span class="badge text-bg-success">Active</span>',
                        '0' => '<span class="badge text-bg-danger">Inactive</span>'
                    ][$value];
                })->sortable()->html(),
        ];
    }

}
