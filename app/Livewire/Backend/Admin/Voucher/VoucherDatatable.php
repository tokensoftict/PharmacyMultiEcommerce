<?php

namespace App\Livewire\Backend\Admin\Voucher;

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

class VoucherDatatable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = Voucher::class;
    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.voucher.update',
            'destroy' => 'backend.admin.voucher.destroy',
            'create'   => 'backend.admin.voucher.create',
        ];


        $this->extraRowAction = [];

        $this->pageHeaderTitle = "Voucher Manager";


        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Voucher Manager",
                'active' =>true
            ]
        ];

    }


    public function mount()
    {
        $this->modalName = "Voucher";

        $this->data = [
            'name' => ['label' => 'Voucher Name', 'type'=>'text'],
            'valid_from' => ['label' => 'Valid From', 'type' =>'datepicker'],
            'valid_to' => ['label' => 'Valid From', 'type' =>'datepicker'],
            'type' => ['label' => 'Voucher Type', 'type' =>'select', 'options' => [
                [
                    'id' => 'Fixed',
                    'text' => 'Fixed'
                ],
                [
                    'id' => 'Percentage',
                    'text' => 'Percentage'
                ]
            ]],
            'type_value' =>['label' => 'Voucher Value', 'type' =>'number'],
            'noofvoucher' =>['label' => 'Number of Voucher', 'type' =>'number'],
            'domain' =>['label' => 'Store', 'type' => 'select', 'options' => [
                [
                    'id' => AppLists::getApp((new WholesalesUser())),
                    'text' => 'Wholesales Store'
                ],
            ]],
            'customer_type_id' => ['label' => 'Customer Type', 'type' => 'select',
                'options' => CustomerType::select('id','name')->where('status', 1)->get()->toArray()
            ],
            'status_id' => ['type' => 'hidden', 'value' => status('Pending'), 'showValue'=> false],
            'customer_group_id' => ['label' => 'Customer Group', 'type' => 'select', 'options' => CustomerGroup::select('id', 'name')->where('status', 1)->get()->toArray()],
            'created_by' => ['label' => 'Created By', 'showValue'=> true ,'type'=>'hidden' ,'display' => auth()->user()->name, 'value' => auth()->id(), 'editCallback' => 'editCreatedCallBack'],
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
            'valid_from' => 'required',
            'valid_to' => 'required',
            'noofvoucher' => 'required',
            'type' => 'required',
            'domain' => 'required',
            'type_value' => 'required',
            'created_by' => 'required'
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }


    public function editCreatedCallBack($value)
    {
        return User::find($value)->name;
    }

    public function builder(): Builder
    {
        return Voucher::query()->with(['status', 'user', 'customer_group', 'customer_type', 'voucher_codes']);
    }

    public static function  mountColumn() : array
    {
        return [
            Column::make("Name", "name")
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
            Column::make("Status", "status_id")
                ->format(function($value, $row, Column $column){
                    return showStatus($value);
                })->html()
                ->sortable(),
            Column::make("Created", "created_at")
                ->sortable(),
            Column::make("Updated", "updated_at")
                ->sortable(),
        ];

    }

    public function onCreate(Voucher &$voucher)
    {
        $noOfVoucher = $voucher->noofvoucher;
        $voucherCodes = [];
        for($i=1; $i<=$noOfVoucher; $i++){
            $voucherCodes[] = new VoucherCode([
                'name' => $voucher->name,
                'type' => $voucher->type,
                'code' => generateRandom(8),
                'domain' => $voucher->domain,
                'created_by' => $voucher->created_by,
                'status_id' => $voucher->status_id,
                'customer_group_id' => $voucher->customer_group_id,
                'customer_type_id' => $voucher->customer_type_id,
                'type_value' => $voucher->type_value,
                'usage_status' => "NOT-USED",
                'valid_from' => $voucher->valid_from,
                'valid_to' => $voucher->valid_to,
                'user_id' => $voucher->user_id,
                'user_type' =>$voucher->user_type,
                'voucher_id' => $voucher->id,
            ]);
        }

        $voucher->voucher_codes()->saveMany($voucherCodes);
    }

    public function onUpdate(Voucher &$voucher)
    {
        $voucher->voucher_codes()->delete();
        $noOfVoucher = $voucher->noofvoucher;
        $voucherCodes = [];
        for($i=1; $i<=$noOfVoucher; $i++){
            $voucherCodes[] = new VoucherCode([
                'name' => $voucher->name,
                'type' => $voucher->type,
                'code' => strtoupper(generateRandom(8)),
                'domain' => $voucher->domain,
                'created_by' => $voucher->created_by,
                'status_id' => $voucher->status_id,
                'customer_group_id' => $voucher->customer_group_id,
                'customer_type_id' => $voucher->customer_type_id,
                'type_value' => $voucher->type_value,
                'usage_status' => "NOT-USED",
                'valid_from' => $voucher->valid_from,
                'valid_to' => $voucher->valid_to,
                'user_id' => $voucher->user_id,
                'user_type' =>$voucher->user_type,
                'voucher_id' => $voucher->id,
            ]);
        }

        $voucher->voucher_codes()->saveMany($voucherCodes);
    }


}
