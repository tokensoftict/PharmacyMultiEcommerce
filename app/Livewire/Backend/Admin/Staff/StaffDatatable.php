<?php

namespace App\Livewire\Backend\Admin\Staff;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Models\Staff;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StaffDatatable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal {
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

    protected $model = Staff::class;

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.staff.update',
            'destroy' => 'backend.admin.staff.destroy',
            'create' => 'backend.admin.staff.create',
        ];

        $this->pageHeaderTitle = "Staff Management";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix . 'admin.dashboard'),
                'name' => "Dashboard",
                'active' => false
            ],
            [
                'name' => "Staff Management",
                'active' => true
            ]
        ];
    }

    public function mount()
    {
        $this->modalName = "Staff";

        $this->data = [
            'name' => ['label' => 'Staff Name', 'type' => 'text'],
            'department' => [
                'label' => 'Department',
                'type' => 'select',
                'options' => [
                    ['id' => 'Retail', 'text' => 'Retail (Supermarket)'],
                    ['id' => 'Wholesales', 'text' => 'Wholesales']
                ]
            ],
            'status' => [
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['id' => 1, 'text' => 'Active'],
                    ['id' => 0, 'text' => 'Inactive']
                ]
            ],
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
            'department' => 'required|in:Retail,Wholesales',
            'status' => 'required|boolean',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }

    public function builder(): Builder
    {
        return Staff::query();
    }

    public static function mountColumn(): array
    {
        return [
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Department", "department")
                ->sortable()
                ->searchable(),
            Column::make("Status", "status")
                ->format(function ($value, $row, Column $column) {
                    return $value ? '<span class="badge badge-phoenix badge-phoenix-success">Active</span>' : '<span class="badge badge-phoenix badge-phoenix-danger">Inactive</span>';
                })->html()
                ->sortable(),
            Column::make("Created At", "created_at")
                ->sortable(),
        ];
    }
}
