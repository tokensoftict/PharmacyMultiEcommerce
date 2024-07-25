<?php

namespace App\Livewire\Backend\Admin\Coupon;

use App\Classes\AppLists;
use App\Classes\ExportDataTableComponent;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Coupon;

class CouponDatatable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = Coupon::class;

    public string $couponCode = "";

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.coupon.update',
            'destroy' => 'backend.admin.coupon.destroy',
            'create'   => 'backend.admin.coupon.create',
        ];


        $this->extraRowAction = [];

        $this->pageHeaderTitle = "Coupon Manager";


        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Coupon Manager",
                'active' =>true
            ]
        ];

    }


    public function mount()
    {
        $this->couponCode = strtoupper(getRandomString_AlphaNum(5));

        $this->modalName = "Coupon";

        $this->data = [
            'name' => ['label' => 'Coupon Name', 'type'=>'text'],
            'code' => ['label' => 'Coupon Code', 'type'=>'hidden', 'showValue'=> true, 'display' => $this->couponCode,  'value' =>  $this->couponCode],
            'valid_from' => ['label' => 'Valid From', 'type' =>'datepicker'],
            'valid_to' => ['label' => 'Valid From', 'type' =>'datepicker'],
            'noofuse' => ['label' => 'Number of Usage', 'type' =>'number'],
            'type' => ['label' => 'Coupon Type', 'type' =>'select', 'options' => [
                [
                    'id' => 'Fixed',
                    'text' => 'Fixed'
                ],
                [
                    'id' => 'Percentage',
                    'text' => 'Percentage'
                ]
            ]],
            'domain' =>['label' => 'Store', 'type' => 'select', 'options' => [
                [
                    'id' => AppLists::getApp((new WholesalesUser())),
                    'text' => 'Wholesales Store'
                ],
            ]],
            'type_value' =>['label' => 'Coupon Value', 'type' =>'number'],
            'customer_type_id' => ['label' => 'Customer Type', 'type' => 'select',
                'options' => CustomerType::select('id','name')->where('status', 1)->get()->toArray()
            ],
            'status_id' => ['type' => 'hidden', 'value' => status('Pending'), 'showValue'=> false],
            'customer_group_id' => ['label' => 'Customer Group', 'type' => 'select', 'options' => CustomerGroup::select('id', 'name')->where('status', 1)->get()->toArray()],
            'created_by' => ['label' => 'Created By', 'showValue'=> true ,'type'=>'hidden' ,'display' => auth()->user()->name, 'value' => auth()->id(), 'editCallback' => 'editCreatedCallBack'],
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
            'code' => 'required|min:3',
            'valid_from' => 'required',
            'valid_to' => 'required',
            'noofuse' => 'required',
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
        return Coupon::query()->with(['status', 'user', 'customer_group', 'customer_type']);
    }

    public static function  mountColumn() : array
    {
        return [
            Column::make("Name", "name")
                ->sortable(),
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
            Column::make("Type", "type")
                ->sortable(),
            Column::make("Type value", "type_value")
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

}
