<?php

namespace App\Livewire\Backend\Admin\Promotion;

use App\Classes\ApplicationEnvironment;
use App\Classes\AppLists;
use App\Classes\ExportDataTableComponent;
use App\Exports\ExportPromotionTemplate;
use App\Imports\ImportPromotionItems;
use App\Models\App;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use App\Models\Promotion;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PromotionTableComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal, WithFileUploads;

    protected $model = Promotion::class;

    public ?int $promotion = NULL;

    public static String $permissionComponentName = 'promotion_manager';

    public function __construct()
    {

        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.promotion.create',
            'destroy' => 'backend.admin.settings.customer_group.destroy',
            'create'   => 'backend.admin.settings.customer_group.create',
        ];

        $this->extraRowAction = [];

        $this->pageHeaderTitle = "Promotion Lists";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Promotion Lists",
                'active' =>true
            ]
        ];


    }

    public function builder(): Builder
    {
        return Promotion::query()
            ->where('app_id', ApplicationEnvironment::$model_id)
            ->withCount(['promotion_items', 'customer_type', 'customer_group'])
            ->with(['status', 'user', 'customer_group', 'customer_type']);
    }

    public function mount()
    {
        $this->modalName = "Promotion";

        $this->data = [
            'name' => ['label' => 'Promotion Name', 'type'=>'text'],
            'status_id' => ['type' => 'hidden', 'value' => status('Pending'), 'showValue'=> false],
            'customer_group_id' => ['label' => 'Customer Group', 'type' => 'select',
                'options' => CustomerGroup::select('id', 'name')->where('status', 1)->get()->toArray()
            ],
            'customer_type_id' => ['label' => 'Customer Type', 'type' => 'select',
                'options' => CustomerType::select('id','name')->where('status', 1)->get()->toArray()
            ],
            'domain' =>['label' => 'Store', 'type' => 'select', 'options' => [
                [
                    'id' => AppLists::getApp((new WholesalesUser())),
                    'text' => 'Wholesales Store'
                ],
            ]],
            'from_date' => ['label' => 'Valid From', 'type' =>'datepicker'],
            'end_date' => ['label' => 'Valid From', 'type' =>'datepicker'],
            'file' => ['label' => 'Upload Promo Stocks', 'type' =>'file', 'template' => 'exportTemplate', 'templateLabel' => 'Download Template'],
            'created' => ['type' => 'hidden', 'value' => now()->format("Y-m-d"), 'showValue'=> false],
            'user_id' => ['label' => 'Created By', 'showValue'=> true ,'type'=>'hidden' ,'display' => auth()->user()->name, 'value' => auth()->id(), 'editCallback' => 'editCreatedCallBack'],
            'app_id' => ['label' => 'Environment', 'showValue'=> false ,'type'=>'hidden' ,'value' =>ApplicationEnvironment::$model_id]
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
            'from_date' => 'required',
            'end_date' => 'required',
            'file' => 'required',
        ];

        $updateValidationRules = $this->newValidateRules;
        unset($updateValidationRules['file']);
        $this->updateValidateRules = $updateValidationRules;

        $this->initControls();
    }

    public function editCreatedCallBack($value)
    {
        return User::find($value)->name;
    }

    public function editAppIdCallBack($value)
    {
        return App::where('model_id', $value)->first()->name;
    }

    public static function  mountColumn() : array
    {
        return [
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Promo Start", "from_date")
                ->format(function($value, $row, Column $column){
                    return $value->format("Y-m-d");
                })
                ->sortable(),
            Column::make("Promo Ends", "end_date")
                ->format(function($value, $row, Column $column){
                    return $value->format("Y-m-d");
                })
                ->sortable(),
            Column::make("Total items", "end_date")
                ->format(function($value, $row, Column $column){
                    return $row->promotion_items->count();
                }),
            Column::make("Customer Group", "customer_group_id")
                ->format(function($value, $row, Column $column){
                    return $row?->customer_group?->name ?? "";
                })
                ->sortable(),
            Column::make("Customer Type", "customer_type_id")
                ->format(function($value, $row, Column $column){
                    return $row?->customer_type?->name ?? "";
                })
                ->sortable(),
            Column::make("Status", "status_id")
                ->format(function($value, $row, Column $column){
                    return showStatus($value);
                })->html()
                ->sortable(),
        ];
    }


    /**
     * @param Promotion $promotion
     * @return void
     */
    public final function onUpdate(Promotion $promotion)
    {
        if(isset($this->formData['file'])) {
            $promotion->promotion_items()->delete();
            $promotion->promotion_items()->saveMany(
                $this->importPromotionItems($promotion)
            );
            $this->promotion = NULL;
        }
    }


    /**
     * @param Promotion $promotion
     * @return void
     */
    public final function onCreate(Promotion $promotion)
    {
        $items = $this->importPromotionItems($promotion);
        $promotion->promotion_items()->saveMany($items);
        $this->promotion = NULL;
    }


    /**
     * @return void
     */
    public final function onNew()
    {
        $this->promotion = NULL;
    }

    /**
     * @param Promotion $promotion
     * @return void
     */
    public final function onEdit(Promotion $promotion)
    {
        $this->promotion = $promotion->id;
    }

    /**
     * @param Promotion $promotion
     * @return void
     */
    public final function onDestroy(Promotion $promotion)
    {
        $this->promotion = NULL;
        $promotion->promotion_items()->delete();
    }


    /**
     * @param Promotion $promotion
     * @return array
     */
    public final function importPromotionItems(Promotion $promotion) : array
    {
        $importPromotion = new ImportPromotionItems($promotion);
        Excel::import($importPromotion, $this->formData['file']);
        return $importPromotion->getPromotionalItems();
    }


    /**
     * @return BinaryFileResponse
     */
    public final function exportTemplate() : BinaryFileResponse
    {
        if(!is_null($this->promotion)) {
            return Excel::download(new ExportPromotionTemplate($this->promotion), 'stock-promotional-template-' . todaysDate() . '.xlsx');
        }
        else {
            return Excel::download(new ExportPromotionTemplate(), 'stock-promotional-template-' . todaysDate() . '.xlsx');
        }
    }
}
