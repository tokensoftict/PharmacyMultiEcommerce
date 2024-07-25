<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\ProductBanner;
use App\Models\Productcategory;
use App\Models\Slider;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class ProductBannerComponentDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = ProductBanner::class;
    public static String $permissionComponentName = 'product_banner';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.product_banner.update',
            'destroy' => 'backend.admin.settings.product_banner.destroy',
            'create'   => 'backend.admin.settings.product_banner.create',
            'status' => 'backend.admin.settings.product_banner.toggle',
        ];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Product Banners",
                'active' =>true
            ]
        ];

        $this->pageHeaderTitle = "Product Banners";

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
        return ProductBanner::query()->with(['classification', 'manufacturer', 'productgroup']);
    }


    public function mount()
    {
        $this->modalName = "Product Banner";

        $this->data = [
            'title' => ['label' => 'Title', 'type'=>'text'],
            'image' => ['label' => 'Image', 'type'=>'image'],
            'classification_id' =>  ['label' => 'Classifications', 'type'=>'select',
                'options'=> classifications()->toArray()
            ],
            'productgroup_id' =>  ['label' => 'Product Groups', 'type'=>'select',
                'options'=> productgroups()->toArray()
            ],
            'manufacturer_id' =>  ['label' => 'Manufacturers', 'type'=>'select',
                'options'=> manufacturers()->toArray()
            ],
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
            'image' => 'required|min:3'
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Title", "title")
                ->sortable(),
            Column::make('image', 'image')
            ->format(fn($value, $row, Column $column) => '<div class="img-group"><img class="img wd-30 ht-30 rounded-circle" src="'.$value.'"/></div>')->html(),
            Column::make('Classification', 'classification_id')
                ->format(fn($value, $row, Column $column) => $row->classification?->name)->sortable(),
            Column::make('Product Group', 'productgroup_id')
                ->format(fn($value, $row, Column $column) => $row->productgroup?->name)->sortable(),
             Column::make('Manufacturer', 'manufacturer_id')
                 ->format(fn($value, $row, Column $column) => $row->manufacturer?->name)->sortable()
        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.product_banner')]
    public function view()
    {
    }
}
