<?php

namespace App\Livewire\Backend\Admin\Order;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Models\Order;
use App\Models\WholesalesUser;
use App\Models\WholessalesStockPrice;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Carbon\Carbon;
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

        $this->actionPermission = [
            'view' => 'backend.admin.order.view',
        ];

        $this->extraRowAction = ['view'];

        $this->extraRowActionButton = [
            [
                'label' => 'View',
                'type' => 'link',
                'route' => "backend.admin.order.view",
                'permission' => 'view',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-eye-o',
            ]
        ];

        $this->pageHeaderTitle  = "Today's Orders ";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
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
        $order = Order::query()
            ->select("*")
            ->where("orders.app_id", ApplicationEnvironment::$model_id)
            ->with(
                [
                    'status',
                    'customer'
                ]
            );

        if(count($this->filter) > 0){
            $order->whereBetween('orders.created_at', [
                carbonize($this->filter['startDate'])->startOfDay()->toDateTimeString(),
                carbonize($this->filter['stopDate'])->endOfDay()->toDateTimeString(),
            ]);
        } else {
            $order->whereBetween('orders.created_at', [
                now()->startOfDay()->toDateTimeString(),
                now()->endOfDay()->toDateTimeString(),
            ]);
        }

        $order->orderBy("orders.id", "DESC");
        return $order;
    }


    public function mount()
    {
        $this->modalName = "Orders";
        $this->data = [];
    }


    public static function  mountColumn() : array
    {
        $orderColumn = [
            Column::make("Invoice No.", "invoice_no")
                ->searchable()
                ->sortable(),
            Column::make("Order ID", "order_id")
                ->searchable()
                ->sortable(),
            Column::make("First name", "firstname")
                ->searchable()
                ->sortable(),
            Column::make("Last name", "lastname")
                ->searchable()
                ->sortable(),
            Column::make("Status", "status_id")
                ->format(function($value, $row, Column $column){
                    return showStatus($value);
                })->sortable()->html(),
        ];

        if(ApplicationEnvironment::$stock_model == WholessalesStockPrice::class)
        {
            $orderColumn = array_merge($orderColumn, [
                Column::make("Business Name", "customer.business_name")
                    ->format(function($value, $row, Column $column){
                        return $row->customer->business_name;
                    })
                    ->searchable()
                    ->sortable(),
                Column::make("Telephone", "telephone")
                    ->searchable()
                    ->sortable(),
            ]);
        }


        $orderColumn = array_merge($orderColumn, [
            Column::make("No Of Items", "id")
                ->format(fn($value, $row, Column $column) => $row->order_products->count()),
            Column::make("Date", "order_date")
                ->format(fn($value, $row, Column $column) => $row->order_date->format('D, F jS, Y'))
                ->searchable()
                ->sortable(),
            Column::make("Total", "total")
                ->format(fn($value, $row, Column $column) => money($value))
                ->searchable()
                ->sortable(),
        ]);

        return $orderColumn;
    }


}

