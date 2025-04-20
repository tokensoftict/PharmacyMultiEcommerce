<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\BankAccount;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class BankAccountComponentDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = BankAccount::class;
    public static String $permissionComponentName = 'bank_account';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.bank_account.update',
            'destroy' => 'backend.admin.settings.bank_account.destroy',
            'create'   => 'backend.admin.settings.bank_account.create',
            'status' => 'backend.admin.settings.bank_account.toggle',
        ];

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Bank Accounts",
                'active' =>true
            ]
        ];

        $this->pageHeaderTitle = "Bank Accounts";

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
        return BankAccount::query()->with(['bank']);
    }


    public function mount()
    {
        $this->modalName = "Bank Account";

        $this->data = [
            'bank_id' => ['label' => 'Select Bank', 'type'=>'dropDownSelect',
                'options'=> banks()->toArray()
            ],
            'account_name' => ['label' => 'Account Name', 'type'=>'text'],
            'account_number' => ['label' => 'Account Number', 'type'=>'text'],
        ];

        $this->newValidateRules = [
            'account_name' => 'required|min:3',
            'account_number' => 'required|min:10',
            'bank_id' => 'required',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Bank Name", "bank_id")
                ->format(function($value, $row, Column $column){
                    return $row->bank->name;
                })
                ->sortable(),
            Column::make("Account Name", "account_name")
                ->sortable(),
            Column::make("Account Number", "account_number")
                ->sortable(),
        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.bank_account')]
    public function view()
    {
    }
}
