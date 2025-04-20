<?php

namespace App\Livewire\Backend\Admin\Stock;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\CustomerType;
use App\Models\StockRestriction;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use DB;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class ViewStocksInStockRestrictionTableComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    public $id;

    protected $model = Stock::class;

    public static String $permissionComponentName = 'stock_manager';

    public function __construct()
    {
        $this->model = ApplicationEnvironment::$stock_model;

        $this->rowAction = [];

        $this->actionPermission = [
            'view' => 'backend.admin.stock_manager.view',
            'stock.admin_status' => 'backend.admin.stock_manager.admin_status',
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

        $this->pageHeaderTitle = "Restriction Stock Lists";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'backend.admin.stock_manager.stock_restriction'),
                'name' => "Stock Restriction",
                'active' =>false
            ],
            [
                'name' => "Stock Lists",
                'active' =>true
            ]
        ];

        $this->rowSpinner = [
            [
                'label' => 'Status',
                'field' => 'stock.admin_status',
                'handler' => 'admin_status'
            ],
        ];

    }

    public function builder(): Builder
    {
        return StockRestriction::query()->with(['stock.productgroup','stock.'.ApplicationEnvironment::$stock_model_string,'stock.manufacturer','stock.classification','stock.productcategory', 'stock.promotion_item'])
            ->where('group_id', $this->id)->where('group_type', CustomerType::class);
    }

    public function mount()
    {
        $this->modalName = "Stock Manager";

        $this->data = [];
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("ID", "stock.id")->searchable()->sortable(),
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
            Column::make("Price", "stock.".ApplicationEnvironment::$stock_model_string.".price")
                ->format(fn($value, $row, Column $column) => money($value))
                ->sortable(),
            Column::make("Quantity", "stock.".ApplicationEnvironment::$stock_model_string.".quantity")->sortable(),
        ];
    }


    public function admin_status($id) : void
    {
        DB::transaction(function () use ($id){
            $model = $this->model::find($id);
            $model->stock->admin_status = !$model->stock->admin_status;
            $model->stock->save();
        });
        $this->dispatch('$refresh');
    }
}
