<?php

namespace App\Livewire\Backend\Admin\Promotion;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Models\PromotionItem;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class ViewStocksInPromotionTableComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    public $id;

    protected $model = PromotionItem::class;

    public static string $permissionComponentName = 'promotion_manager';

    public function __construct()
    {
        $this->rowAction = [];

        $this->actionPermission = [
            'view' => 'backend.admin.promotion.create',
        ];

        // $this->extraRowAction = ['view'];

        // $this->extraRowActionButton = [
        //     [
        //         'label' => 'View',
        //         'type' => 'link',
        //         'route' => "backend.admin.stock_manager.view",
        //         'permission' => 'view',
        //         'class' => 'btn btn-sm btn-outline-primary',
        //         'icon' => 'fa fa-eye-o',
        //         'parameters' => [
        //             'id' => 'stock_id',
        //         ]
        //     ]
        // ];

        $this->pageHeaderTitle = "Promotion Stock Lists";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix . 'admin.dashboard'),
                'name' => "Dashboard",
                'active' => false
            ],
            [
                'route' => route(ApplicationEnvironment::$storePrefix . 'backend.admin.promotion.list'),
                'name' => "Promotion Lists",
                'active' => false
            ],
            [
                'name' => "Stock Lists",
                'active' => true
            ]
        ];
    }

    public function builder(): Builder
    {
        return PromotionItem::query()
            ->with(['stock.productgroup', 'stock.' . ApplicationEnvironment::$stock_model_string, 'stock.manufacturer', 'stock.classification', 'stock.productcategory'])
            ->where('promotion_id', $this->id);
    }

    public function mount()
    {
        $this->modalName = "Promotion Stock Manager";
        $this->data = [];
    }

    public static function mountColumn(): array
    {
        return [
            Column::make("ID", "stock.id")->searchable()->sortable(),
            Column::make("Name", "stock.name")->searchable()->sortable(),
            Column::make("Category", "stock.productcategory.name")
                ->format(fn($value, $row, Column $column) => $value)
                ->searchable()->sortable(),
            Column::make("Classification", "stock.classification.name")
                ->format(fn($value, $row, Column $column) => $value)
                ->searchable()->sortable(),
            Column::make("Original Price", "stock." . ApplicationEnvironment::$stock_model_string . ".price")
                ->format(fn($value, $row, Column $column) => money($value))
                ->sortable(),
            Column::make("Promo Price", "price")
                ->format(fn($value, $row, Column $column) => money($value))
                ->sortable(),
        ];
    }
}
