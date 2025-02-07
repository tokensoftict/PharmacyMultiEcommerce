<?php

namespace App\Livewire\Backend\Admin\Stock;

use App\Classes\ExportDataTableComponent;
use App\Livewire\Backend\Component\UploadStockSizeComponent;
use App\Models\StockSize;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StockSizeDatatable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = StockSize::class;

    public static String $permissionComponentName = 'stock_size';


    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.stock_manager.stock_size.update',
            'destroy' => 'backend.admin.stock_manager.stock_size.destroy',
            'create'   => 'backend.admin.stock_manager.stock_size.create',
            'upload_stock_size'=> 'backend.admin.stock_manager.stock_size.upload'
        ];

        $this->extraRowAction = [];

        $this->toolbarButtons = [
          UploadStockSizeComponent::class =>[
              'label' => 'Upload Stock Size',
              'type' => 'component',
              'route' => "backend.admin.stock_manager.stock_size.upload",
              'permission' => 'upload_stock_size',
              'class' => 'btn btn-sm btn-outline-primary',
              'icon' => 'fa fa-cloud',
              'component' => UploadStockSizeComponent::class,
              'is' => 'modal',
              'modal' => 'pages.backend.admin.component.upload-stock-size-component',
              'parameters' =>[]
          ]
        ];


        $this->pageHeaderTitle = "Stock Size Manager";

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Stock Size Manager",
                'active' =>true
            ]
        ];

    }

    public function builder(): Builder
    {
        return StockSize::query()->with(['stock', 'stock.productcategory']);
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Stock", "stock_id")
                ->format(function($value, $row, Column $column){
                    return $row?->stock?->name;
                })
                ->sortable(),
            Column::make("Category", "stock_id")
                ->format(function($value, $row, Column $column){
                    return $row->stock->productcategory->name ?? "N/A";
                })
                ->sortable(),
            Column::make("Size", "product_size")
                ->sortable(),
        ];
    }

    public function mount()
    {
        $this->modalName = "Stock Size";

        $this->data = [
            'stock_id' => ['label' => 'Stock', 'type'=>'stockComponent', 'placeholder' => 'Search and Select Stock', 'id'=>'stock_id_component'],
            'product_size' => ['label' => 'Size', 'type'=>'text'],
        ];

        $this->newValidateRules = [
            'stock_id' => 'required',
            'product_size' => 'required',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }

}
