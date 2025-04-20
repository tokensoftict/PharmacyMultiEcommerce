<?php

namespace App\Livewire\Backend\Admin\Voucher;

use App\Classes\ApplicationEnvironment;
use App\Classes\AppLists;
use App\Classes\ExportDataTableComponent;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherCode;
use App\Models\WholesalesUser;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Coupon;

class VoucherCodesDatatable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = VoucherCode::class;

    public $id;

    public function __construct()
    {
        $this->rowAction = ['destroy'];

        $this->actionPermission = [
            'destroy' => 'backend.admin.voucher.destroy',
        ];


        $this->extraRowAction = [];


        $this->extraRowActionButton = [];


        $this->pageHeaderTitle = "";


        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'backend.admin.voucher.list'),
                'name' => "Voucher List",
                'active' =>false
            ],
            [
                'name' => "Voucher Codes Report",
                'active' =>true
            ]
        ];

    }

    public function mount()
    {
        $this->modalName = "Voucher";
        $this->data = [];
    }

    public function builder(): Builder
    {
        return VoucherCode::query()->select("*")->with(['status', 'user', 'customer_group', 'customer'])->where("voucher_codes.voucher_id", $this->id);
    }

    public static function  mountColumn() : array
    {
        return [
            Column::make("Code", "code")
                ->sortable(),
            Column::make("Valid from", "valid_from")
                ->format(function($value, $row, Column $column){
                    return $value->format("Y-m-d");
                })
                ->sortable(),
            Column::make("Valid to", "valid_to")
                ->format(function($value, $row, Column $column){
                    return $value->format("Y-m-d");
                })
                ->sortable(),
            Column::make("Voucher Type", "type")
                ->sortable(),
            Column::make("Voucher value", "type_value")
                ->sortable(),
            Column::make("Status", "usage_status")
                ->sortable(),
            Column::make("Used By", "customer_type")
                ->format(function($value, $row, Column $column){
                    return $row->customer ? ($row->customer->business_name ?? $row->customer->user->name) : "-";
                })
                ->sortable(),
        ];

    }


}
