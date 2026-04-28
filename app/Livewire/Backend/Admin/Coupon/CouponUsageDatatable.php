<?php

namespace App\Livewire\Backend\Admin\Coupon;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Exports\CouponUsageExport;
use App\Models\CouponUsageHistory;
use App\Traits\DynamicDataTableExport;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CouponUsageDatatable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport {
        DynamicDataTableExport::bulkActions insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::export_all insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::export_selected insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::getExportColumns insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::getExportFields insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::prepareExport insteadof SimpleDatatableComponentTrait;
        DynamicDataTableExport::renderValue insteadof SimpleDatatableComponentTrait;
    }

    protected $model = CouponUsageHistory::class;

    public $coupon_id;

    public function mount($id)
    {
        $this->coupon_id = $id;
        $this->rowAction = [];
        $this->extraRowAction = [];

        $this->pageHeaderTitle = "Coupon Usages";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix . 'admin.dashboard'),
                'name' => "Dashboard",
                'active' => false
            ],
            [
                'route' => route(ApplicationEnvironment::$storePrefix . 'backend.admin.coupon.list'),
                'name' => "Coupon Manager",
                'active' => false
            ],
            [
                'name' => "Coupon Usages",
                'active' => true
            ]
        ];
    }

    public function builder(): Builder
    {
        return CouponUsageHistory::query()
            ->with(['customer.user', 'app'])
            ->where('coupon_id', $this->coupon_id);
    }

    public static function mountColumn(): array
    {
        return [
            Column::make("Coupon Code", "code")
                ->sortable()
                ->searchable(),
            Column::make("Customer", "user_type_id")
                ->format(function ($value, $row, Column $column) {
                    if ($row->customer && $row->customer->user) {
                        return $row->customer->user->name;
                    }
                    return 'Unknown';
                })->html(),
            Column::make("Customer Phone", "user_type_type")
                ->format(function ($value, $row, Column $column) {
                    if ($row->customer && $row->customer->user) {
                        return $row->customer->user->phone ?? ($row->customer->phone ?? 'Unknown');
                    }
                    return 'Unknown';
                }),
            Column::make("Environment", "app_id")
                ->format(function ($value, $row, Column $column) {
                    return $row->app ? $row->app->name : 'Unknown';
                }),
            Column::make("Use Date", "use_date")
                ->format(function ($value, $row, Column $column) {
                    return $value ? $value->format("Y-m-d H:i:s") : 'N/A';
                })
                ->sortable(),
        ];
    }

    public function export_all()
    {
        $usages = $this->builder()->get();
        return \Maatwebsite\Excel\Facades\Excel::download(new CouponUsageExport($usages), 'coupon_usages_report.xlsx');
    }

    public function export_selected()
    {
        $usages = $this->builder()->whereIn('id', $this->getSelected())->get();
        return \Maatwebsite\Excel\Facades\Excel::download(new CouponUsageExport($usages), 'coupon_usages_report.xlsx');
    }
}
