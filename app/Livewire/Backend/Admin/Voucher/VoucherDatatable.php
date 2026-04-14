<?php

namespace App\Livewire\Backend\Admin\Voucher;

use App\Classes\ApplicationEnvironment;
use App\Classes\AppLists;
use App\Classes\ExportDataTableComponent;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherCode;
use App\Models\WholesalesUser;
use App\Models\VoucherStock;
use App\Exports\CouponStockTemplateExport;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Column;

class VoucherDatatable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal, WithFileUploads {
        DynamicDataTableExport::bulkActions insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::export_all insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::export_selected insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::getExportColumns insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::getExportFields insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::prepareExport insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::renderValue insteadof SimpleDatatableComponentTrait;
        DynamicDataTableFormModal::destroy insteadof SimpleDatatableComponentTrait;
        DynamicDataTableFormModal::edit insteadof SimpleDatatableComponentTrait;
        DynamicDataTableFormModal::toggle insteadof SimpleDatatableComponentTrait;
    }

    protected $model = Voucher::class;
    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.voucher.update',
            'destroy' => 'backend.admin.voucher.destroy',
            'create'   => 'backend.admin.voucher.create',
            'view_report' => 'backend.admin.voucher.view_report'
        ];


        $this->extraRowAction = [];


        $this->extraRowActionButton = [
            [
                'label' => 'Approve',
                'icon' => 'fa fa-check',
                'class' => 'btn btn-sm btn-phoenix-success',
                'type' => 'method',
                'method' => 'approve',
                'permission' => 'backend.admin.voucher.approve',
                'visible' => 'isPending'
            ],
            [
                'label' => 'View Codes',
                'type' => 'link',
                'route' => "backend.admin.voucher.view_report",
                'permission' => 'view_report',
                'class' => 'btn btn-sm btn-outline-primary',
                'icon' => 'fa fa-eye-o',
                'parameters' => [
                    'id' =>'id'
                ]
            ]
        ];


        $this->pageHeaderTitle = "Voucher Manager";


        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
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
            'minimum_amount' =>['label' => 'Minimum Order Amount', 'type' =>'number'],
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
            'stock_excel' => [
                'label' => 'Specific Stock (Excel)',
                'type' => 'file',
                'showValue'=> false,
                'template' => 'downloadTemplate',
                'templateLabel' => 'Download Template'
            ],
            'customer_group_id' => ['label' => 'Customer Group', 'type' => 'select', 'options' => CustomerGroup::select('id', 'name')->where('status', 1)->get()->toArray()],
            'created_by' => ['label' => 'Created By', 'showValue'=> true ,'type'=>'hidden' ,'display' => auth()->user()->name, 'value' => auth()->id(), 'editCallback' => 'editCreatedCallBack'],
            'app_id' => ['label' => 'Environment', 'showValue'=> false ,'type'=>'hidden' ,'value' =>ApplicationEnvironment::$model_id]
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

    public function isPending($row)
    {
        return $row->status_id == status('Pending');
    }

    public function downloadTemplate()
    {
        return Excel::download(new CouponStockTemplateExport, 'voucher_stock_template.xlsx');
    }

    public function approve($id)
    {
        $voucher = Voucher::find($id);
        $voucher->status_id = status('Approved');
        $voucher->save();
        $this->refreshTable();
    }

    private function processExcel($voucher)
    {
        if (isset($this->formData['stock_excel']) && $this->formData['stock_excel']) {
            $path = $this->formData['stock_excel']->getRealPath();
            $data = Excel::toArray(new class {}, $path);

            if (count($data) > 0 && count($data[0]) > 0) {
                // Clear existing stocks if updating
                $voucher->voucherStocks()->delete();

                foreach ($data[0] as $index => $row) {
                    // Skip header if it looks like one
                    if ($index === 0 && !is_numeric($row[0])) continue;

                    $localStockId = $row[0];
                    if ($localStockId) {
                        VoucherStock::create([
                            'voucher_id' => $voucher->id,
                            'local_stock_id' => $localStockId
                        ]);
                    }
                }
            }
        }
    }

    public function builder(): Builder
    {
        return Voucher::query()->with(['status', 'user', 'customer_group', 'customer_type', 'voucher_codes'])->where('app_id',ApplicationEnvironment::$model_id);
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
                'app_id' => $voucher->app_id,
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
                'minimum_amount' => $voucher->minimum_amount,
            ]);
        }

        $voucher->voucher_codes()->saveMany($voucherCodes);
        $this->processExcel($voucher);
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
                'app_id' => $voucher->app_id,
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
                'minimum_amount' => $voucher->minimum_amount,
            ]);
        }

        $voucher->voucher_codes()->saveMany($voucherCodes);
        $this->processExcel($voucher);
    }


}
