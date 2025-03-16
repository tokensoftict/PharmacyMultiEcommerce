<?php

namespace App\Livewire\Backend\Admin\Stock;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use DB;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class StockManagerDataTableComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = Stock::class;

    public static String $permissionComponentName = 'stock_manager';

    public function __construct()
    {
        $this->model = ApplicationEnvironment::$stock_model;

        $this->rowAction = [];

        $this->actionPermission = [
            'view' => 'backend.admin.stock_manager.view',
            'featured' => 'backend.admin.stock_manager.set_featured',
            'special_offer' => 'backend.admin.stock_manager.special_offer',
        ];

        $this->extraRowAction = ['view'];

        $this->extraRowActionButton = [
            [
                'label' => 'View',
                'type' => 'link',
                'route' => "backend.admin.stock_manager.view",
                'permission' => 'view',
                'class' => 'btn btn-sm btn-outline-primary',
                'icon' => 'fa fa-eye-o',
            ]
        ];

        $this->pageHeaderTitle = "Stock Lists";

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Stock Lists",
                'active' =>true
            ]
        ];

        $this->rowSpinner = [
            [
                'label' => 'Featured',
                'field' => 'featured',
                'handler' => 'featured'
            ],
            [
                'label' => 'Special Offer',
                'field' => 'special_offer',
                'handler' => 'special_offer'
            ]
        ];

    }

    public function builder(): Builder
    {
        return ApplicationEnvironment::$stock_model::query()->with(['stock.productgroup','stock.manufacturer','stock.classification','stock.productcategory', 'stock.promotion_item']);
        //return Stock::query()->with(['productgroup','manufacturer','classification','productcategory', 'stock_price']);
    }

    public function mount()
    {
        $this->modalName = "Stock Manager";

        $this->data = [];
    }


    public static function  mountColumn() : array
    {
        return [
            ImageColumn::make('Image', 'stock.image')
                ->location(
                    fn($row) => $row->image === NULL ?  asset('logo/placholder.jpg') : asset('images/'.$row->image)
                ) ->attributes(fn($row) => [
                    'class' => 'd-block  rounded-5',
                    'alt' => $row->name,
                ]),
            Column::make("Name", "stock.name")->searchable()->sortable(),
            Column::make("Category", "stock.productcategory.name")
                ->format(fn($value, $row, Column $column) => $value)
                ->searchable()->sortable(),
            Column::make("Classification", "stock.classification.name")
                ->format(fn($value, $row, Column $column) => $value)
                ->searchable()->sortable(),
            Column::make("Price", "price")
                ->format(fn($value, $row, Column $column) => money($value))
                ->sortable(),
            Column::make("Quantity", "quantity")->sortable(),
        ];
    }

    public function special_offer($id) : void
    {
        DB::transaction(function () use ($id){
            $model = $this->model::find($id);
            $model->special_offer = !$model->special_offer;
            $model->save();
        });
        $this->dispatch('$refresh');
    }

    public function featured($id) : void
    {
        DB::transaction(function () use ($id){
            $model = $this->model::find($id);
            $model->featured = !$model->featured;
            $model->save();
        });
        $this->dispatch('$refresh');
    }
}
