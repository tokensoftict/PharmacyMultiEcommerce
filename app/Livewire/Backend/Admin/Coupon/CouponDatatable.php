<?php

namespace App\Livewire\Backend\Admin\Coupon;

use App\Classes\ApplicationEnvironment;
use App\Classes\AppLists;
use App\Classes\ExportDataTableComponent;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use App\Models\PushNotification;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Models\CouponStock;
use App\Exports\CouponStockTemplateExport;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Coupon;

class CouponDatatable extends ExportDataTableComponent
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

    protected $model = Coupon::class;

    public string $couponCode = "";

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.coupon.update',
            'destroy' => 'backend.admin.coupon.destroy',
            'create' => 'backend.admin.coupon.create',
        ];


        $this->extraRowAction = [];

        $this->extraRowActionButton = [
            [
                'label' => 'Approve',
                'icon' => 'fa fa-check',
                'class' => 'btn btn-sm btn-phoenix-success',
                'type' => 'method',
                'method' => 'approve',
                'permission' => 'backend.admin.coupon.approve',
                'visible' => 'isPending'
            ],
            [
                'label' => 'Usages',
                'icon' => 'fa fa-list',
                'class' => 'btn btn-sm btn-phoenix-info',
                'type' => 'link',
                'route' => 'admin.coupon.usages',
                'permission' => 'backend.admin.coupon.create'
            ]
        ];

        $this->pageHeaderTitle = "Coupon Manager";


        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix . 'admin.dashboard'),
                'name' => "Dashboard",
                'active' => false
            ],
            [
                'name' => "Coupon Manager",
                'active' => true
            ]
        ];

    }


    public function mount()
    {
        $this->couponCode = strtoupper(getRandomString_AlphaNum(5));

        $this->modalName = "Coupon";

        $this->data = [
            'name' => ['label' => 'Coupon Name', 'type' => 'text'],
            'code' => ['label' => 'Coupon Code', 'type' => 'text', 'default' => $this->couponCode],
            'valid_from' => ['label' => 'Valid From', 'type' => 'datepicker'],
            'valid_to' => ['label' => 'Valid From', 'type' => 'datepicker'],
            'noofuse' => ['label' => 'Number of Usage', 'type' => 'number'],
            'type' => [
                'label' => 'Coupon Type',
                'type' => 'select',
                'options' => [
                    [
                        'id' => 'Fixed',
                        'text' => 'Fixed'
                    ],
                    [
                        'id' => 'Percentage',
                        'text' => 'Percentage'
                    ]
                ]
            ],
            'type_value' => ['label' => 'Coupon Value', 'type' => 'number'],
            'minimum_amount' => ['label' => 'Minimum Order Amount', 'type' => 'number', 'default' => 0],
            'customer_type_id' => [
                'label' => 'Customer Type',
                'type' => 'select',
                'options' => CustomerType::select('id', 'name')->where('status', 1)->get()->toArray()
            ],
            'status_id' => ['type' => 'hidden', 'value' => status('Pending'), 'showValue' => false],
            'stock_excel' => [
                'label' => 'Specific Stock (Excel)',
                'type' => 'file',
                'showValue' => false,
                'template' => 'downloadTemplate',
                'templateLabel' => 'Download Template'
            ],
            'customer_group_id' => ['label' => 'Customer Group', 'type' => 'select', 'options' => CustomerGroup::select('id', 'name')->where('status', 1)->get()->toArray()],
            'created_by' => ['label' => 'Created By', 'showValue' => true, 'type' => 'hidden', 'display' => auth()->user()->name, 'value' => auth()->id(), 'editCallback' => 'editCreatedCallBack'],
            'app_id' => ['label' => 'Environment', 'showValue' => false, 'type' => 'hidden', 'value' => ApplicationEnvironment::$model_id]
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

    public function isPending($row)
    {
        return $row->status_id == status('Pending');
    }

    public function downloadTemplate()
    {
        return Excel::download(new CouponStockTemplateExport, 'coupon_stock_template.xlsx');
    }

    public function approve($id)
    {
        $coupon = Coupon::find($id);
        $coupon->status_id = status('Approved');
        $coupon->save();
        $this->refreshTable();
    }

    public function onCreate($coupon)
    {
        $this->processExcel($coupon);
    }

    public function onUpdate($coupon)
    {
        $this->processExcel($coupon);
    }

    private function processExcel($coupon)
    {
        if (isset($this->formData['stock_excel']) && $this->formData['stock_excel']) {
            $path = $this->formData['stock_excel']->getRealPath();
            $data = Excel::toArray(new class {}, $path);

            if (count($data) > 0 && count($data[0]) > 0) {
                // Clear existing stocks if updating
                $coupon->couponStocks()->delete();

                foreach ($data[0] as $index => $row) {
                    // Skip header if it looks like one
                    if ($index === 0 && !is_numeric($row[0]))
                        continue;

                    $localStockId = $row[0];
                    if ($localStockId) {
                        CouponStock::create([
                            'coupon_id' => $coupon->id,
                            'local_stock_id' => $localStockId
                        ]);
                    }
                }
            }
        }
    }

    public function builder(): Builder
    {
        return Coupon::query()->with(['status', 'user', 'customer_group', 'customer_type'])->where('app_id', ApplicationEnvironment::$model_id);
    }

    public static function mountColumn(): array
    {
        return [
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Code", "code")
                ->sortable(),
            Column::make("Valid from", "valid_from")
                ->format(function ($value, $row, Column $column) {
                    return $value->format("Y-m-d");
                })
                ->sortable(),
            Column::make("Valid to", "valid_to")
                ->format(function ($value, $row, Column $column) {
                    return $value->format("Y-m-d");
                })
                ->sortable(),
            Column::make("Type", "type")
                ->sortable(),
            Column::make("Type value", "type_value")
                ->sortable(),
            Column::make("Status", "status_id")
                ->format(function ($value, $row, Column $column) {
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
