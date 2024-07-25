<?php

namespace App\Livewire\Backend\Admin\Order;

use App\Classes\ExportDataTableComponent;
use App\Models\Order;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class OrderDataTableComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = Order::class;

    public static String $permissionComponentName = 'order_manager';

    public function __construct()
    {

        $this->rowAction = [];

        $this->actionPermission = [];

        $this->extraRowAction = [];

        $this->pageHeaderTitle = "Order Lists";

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Order Lists",
                'active' =>true
            ]
        ];


    }

    public function builder(): Builder
    {
        return Order::query()->where('domain', request()->getHost())->withCount(['order_products'])->with(['status', 'address', 'customer_group', 'delivery_method', 'payment_method', 'sales_representative']);
    }

    public function mount()
    {
        $this->modalName = "Orders";

        $this->data = [];
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Invoice No.", "invoice_no")
                ->searchable()
                ->sortable(),
            Column::make("Order ID", "invoice_no")
                ->searchable()
                ->sortable(),
            Column::make("No Of Items", "order_products_count")
                ->searchable()
                ->sortable(),
            Column::make("Date", "order_date")
                ->searchable()
                ->sortable(),
            Column::make("Total", "total")
                ->searchable()
                ->sortable(),
            Column::make("Status", "status_id")
                ->searchable()
                ->sortable(),
        ];
    }


}
