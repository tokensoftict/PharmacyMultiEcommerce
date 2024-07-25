<?php

namespace App\Livewire\Backend\Admin\Customer;

use App\Classes\ExportDataTableComponent;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\CustomerSearchHistory;

class CustomerSearchHistoryDataTable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = CustomerSearchHistory::class;

    public function __construct(){

        $this->rowAction = ['destroy'];

        $this->actionPermission = [
            'delete_all_histories'=> 'backend.admin.settings.customer_manager.customer_search_history.destroy.all'
        ];

        $this->extraRowAction = [];

        $this->toolbarButtons = [];

        $this->pageHeaderTitle = "Customer Search Histories";

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Customer Search Histories",
                'active' =>true
            ]
        ];
    }

    public function mount()
    {
        $this->data = [];
    }

    public function builder(): Builder
    {
        return CustomerSearchHistory::query()->with(['productcategory'])->join('productcategories', 'productcategories.id','=', 'customer_search_histories.productcategory_id');
    }

    /**
     * @return array
     */
    public static function  mountColumn() : array
    {
        return [
            Column::make("Keyword", "keyword")
                ->sortable(),
            Column::make("Product Category", "productcategory_id")
                ->format(function($value, $row, Column $column){
                    return $row?->productcategory?->name ?? "";
                })
                ->sortable(),
            Column::make("Date added", "date_added")
                ->sortable(),
            Column::make("Ip Address", "ip")
                ->sortable(),
        ];
    }

}
