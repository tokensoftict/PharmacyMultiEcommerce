<?php

namespace App\Livewire\Backend\Admin\Stock;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
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

        $this->rowAction = [];

        $this->actionPermission = [];

        $this->extraRowAction = [];

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
            Column::make("Last Updated", "updated_at")->sortable(),
        ];
    }


    #[PermissionAttribute('View', 'view', 'backend.admin.stock_manager.list_stock')]
    public function view()
    {

    }
}
