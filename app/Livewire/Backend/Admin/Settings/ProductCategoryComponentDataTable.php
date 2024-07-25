<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\Productcategory;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class ProductCategoryComponentDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = Productcategory::class;
    public static String $permissionComponentName = 'product_category';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.product_category.update',
            'destroy' => 'backend.admin.settings.product_category.destroy',
            'create'   => 'backend.admin.settings.product_category.create',
            'status' => 'backend.admin.settings.product_category.toggle',
        ];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Product Categories",
                'active' =>true
            ]
        ];

        $this->pageHeaderTitle = "Product Categories";

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
        return Productcategory::query();
    }


    public function mount()
    {
        $this->modalName = "Product Category";

        $this->data = [
            'name' => ['label' => 'Name', 'type'=>'text'],
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Name", "name")
                ->sortable(),
        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.product_category')]
    public function view()
    {
    }
}
