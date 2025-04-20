<?php

namespace App\Livewire\Backend\Admin\Customer\Wholesales;

use App\Classes\ApplicationEnvironment;
use App\Livewire\Backend\Component\WholeSales\WholeSalesCustomerFormComponent;
use App\Models\Address;
use App\Models\AppUser;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
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

    protected $model = WholesalesUser::class;

    public static String $permissionComponentName = 'wholesales_customer_manager';

    public function __construct()
    {
        $this->rowAction = ['edit'];

        $this->actionPermission = [
            'create_new_customer'=> 'backend.admin.customer_manager.wholesales.create',
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
            WholeSalesCustomerFormComponent::class =>[
                'label' => 'New Customer',
                'type' => 'component',
                'route' => "backend.admin.customer_manager.wholesales.create",
                'permission' => 'create_new_customer',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-plus',
                'component' => WholeSalesCustomerFormComponent::class,
                'is' => 'modal',
                'modal' => 'backend.component.whole-sales.whole-sales-customer-form-component',
                'parameters' =>[]
            ]
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
        return WholesalesUser::query()->with(['customer_group', 'user', 'customer_type']);
    }

    public function mount()
    {
        $this->modalName = "Customer";

        $this->data = [
            'business_name' => ['label' => 'Business Name', 'type'=>'text'],
            'customer_type_id' => ['label' => 'Customer Type', 'type' => 'select', 'options' => CustomerType::select('id', 'name')->where('status', 1)->get()->toArray()],
            'customer_group_id' => ['label' => 'Customer Group', 'type' => 'select', 'options' => CustomerGroup::select('id', 'name')->where('status', 1)->get()->toArray()],
            'phone' => ['label' => 'Business Phone Number', 'type'=>'text'],
            //'address_id' => ['label' => 'Address', 'type' => 'select', 'options' => []],
           // 'sales_representative_id' => ['label' => 'Sales Representative', 'type'=>'text'],
        ];

        $this->newValidateRules = [
            'business_name' => 'required|min:3',
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
            Column::make("Business Name", "business_name")->sortable(),
            Column::make("Type", "customer_type.name")
                ->format(fn($value, $row, Column $column) => $value)
                ->sortable(),
            Column::make("Group", "customer_group.name")
                ->format(fn($value, $row, Column $column) => $value)
                ->sortable(),
            Column::make("Phone", "user.phone")->sortable(),
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
