<?php

namespace App\Livewire\Backend\Admin\Customer\Wholesales;

use App\Livewire\Backend\Component\WholeSales\WholeSalesCustomerFormComponent;
use App\Models\AppUser;
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

    protected $model = AppUser::class;

    public static String $permissionComponentName = 'wholesales_customer_manager';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'create_new_customer'=> 'backend.admin.settings.customer_manager.wholesales.create'
        ];

        $this->extraRowAction = [];

        $this->toolbarButtons = [
            WholeSalesCustomerFormComponent::class =>[
                'label' => 'New Customer',
                'type' => 'component',
                'route' => "backend.admin.settings.customer_manager.wholesales.create",
                'permission' => 'create_new_customer',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-plus',
                'component' => WholeSalesCustomerFormComponent::class,
                'is' => 'modal',
                'modal' => 'pages.backend.admin.component.whole-sales.whole-sales-customer-form-component',
                'parameters' =>[]
            ]
        ];

        $this->pageHeaderTitle = "Customer Lists";

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
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
        return WholesalesUser::query()->with(['customer_group'])
            ->join('users','wholesales_users.user_id','=', 'users.id');
    }

    public function mount()
    {
        $this->data = [];
        $this->modalName = "Wholesales Customer Manager";
    }

    public static function  mountColumn() : array
    {
        return [
            Column::make("First Name", "firstname")->sortable(),
            Column::make("Last Name", "lastname")->sortable(),
            Column::make("Email", "email")->sortable(),
            Column::make("Business Name", "business_name")->sortable(),
            Column::make("Type", "type")->sortable(),
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
            Column::make("Group", "customer_group_id")
                ->format(fn($value, $row, Column $column) => $row?->customer_group?->name)
                ->sortable(),
        ];
    }

}
