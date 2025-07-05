<?php

namespace App\Livewire\Backend\Admin\PushNotification;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Exports\ExportPushNotificationTemplate;
use App\Imports\ImportPushNotificationItems;
use App\Models\CustomerGroup;
use App\Models\CustomerType;
use App\Models\PushNotificationCustomer;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Livewire\WithFileUploads;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PushNotification;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class PushNotificationDatatable extends ExportDataTableComponent
{
    use WithFileUploads;

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = PushNotification::class;

    public ?int $pushNotification = NULL;

    public function __construct()
    {
        $this->rowAction = ['edit'];

        $this->actionPermission = [
            'edit' => 'backend.admin.med_reminder.update',
            'destroy' => 'backend.admin.med_reminder.destroy',
            'create'   => 'backend.admin.med_reminder.create',
            'view' => 'backend.admin.med_reminder.view_report',
        ];


        $this->extraRowAction = ['view'];

        $this->extraRowActionButton = [
            [
                'label' => 'View',
                'type' => 'link',
                'route' => "backend.admin.push_notification.view",
                'permission' => 'view',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-eye-o',
            ]
        ];

        $this->pageHeaderTitle = "Push Notification Lists";


        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Push Notifications Lists",
                'active' =>true
            ]
        ];

    }

    public function mount()
    {
        $this->modalName = "Push Notification";

        $this->data = [
            'title' => ['label' => 'Title', 'type'=>'text'],
            'body' => ['label' => 'Body', 'type'=>'textarea'],
            'status' => ['type' => 'hidden', 'value' => "DRAFT", 'showValue'=> false],
            'file' => ['label' => 'Upload Notification Stocks', 'type' =>'file', 'template' => 'exportTemplate', 'templateLabel' => 'Download Template'],
            'customer_group_id' => ['label' => 'Customer Group', 'type' => 'select',
                'options' => CustomerGroup::select('id', 'name')->where('status', 1)->get()->toArray()
            ],
            'customer_type_id' => ['label' => 'Customer Type', 'type' => 'select',
                'options' => CustomerType::select('id','name')->where('status', 1)->get()->toArray()
            ],
            'user_id' => ['label' => 'Created By', 'showValue'=> true ,'type'=>'hidden' ,'display' => auth()->user()->name, 'value' => auth()->id(), 'editCallback' => 'editCreatedCallBack'],
            'app_id' => ['label' => 'Environment', 'showValue'=> false ,'type'=>'hidden' ,'value' =>ApplicationEnvironment::$model_id]
        ];

        $this->newValidateRules = [
            'title' => 'required|min:3',
            'body' => 'required|min:3',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }

    public function builder(): Builder
    {
        return PushNotification::query()
            ->where('app_id', ApplicationEnvironment::$model_id)
            ->with(['user'])->orderBy('id', 'desc');
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Title", "title")
                ->sortable(),
            Column::make("Body", "body")
                ->sortable(),
            Column::make("No of Customer", "no_of_device")
                ->format(function($value, $row, Column $column){
                    return $row->push_notification_customers->count();
                }),
            Column::make("No of Stock", "no_of_device")
                ->format(function($value, $row, Column $column){
                    return $row->stocks->count();
                }),
            Column::make("Total view", "total_view")
                ->sortable(),
            Column::make("Total sent", "total_sent")
                ->sortable(),
            Column::make("Status", "status")
                ->format(function($value, $row, Column $column){
                    $label = [
                        'DRAFT' => 'info',
                        'APPROVED' => 'primary',
                        'SENT' => 'success',
                        'CANCEL' => 'danger',
                    ];
                    return label(ucwords($value), $label[$value], 'lg');
                })->html()
                ->sortable(),
            Column::make("Created at", "created_at")

        ];
    }

    public function editCreatedCallBack($value)
    {
        return User::find($value)->name;
    }


    public function exportTemplate()
    {
        if(!is_null($this->pushNotification)) {
            return Excel::download(new ExportPushNotificationTemplate($this->pushNotification), 'push-notification-template-' . todaysDate() . '.xlsx');
        }
        else {
            return Excel::download(new ExportPushNotificationTemplate(), 'push-notification-template-' . todaysDate() . '.xlsx');
        }
    }


    /**
     * @param PushNotification $pushNotification
     * @return array
     */
    public final function ImportPushNotificationItems(PushNotification $pushNotification) : array
    {
        $importPushNotification = new ImportPushNotificationItems($pushNotification);
        Excel::import($importPushNotification, $this->formData['file']);
        return $importPushNotification->getPushNotificationItems();
    }


    /**
     * @param PushNotification $pushNotification
     * @return void
     */
    public final function onUpdate(PushNotification $pushNotification)
    {
        if(isset($this->formData['file'])) {
            $pushNotification->stocks()->delete();
            $items = $this->ImportPushNotificationItems($pushNotification);
            foreach ($items as $item) {
                $item->save();
            }

        }

        $pushNotification->push_notification_customers()->delete();
        $pushNotification->push_notification_customers()->saveMany(
            $this->prepareAndSaveCustomers($pushNotification)
        );

        $this->pushNotification = NULL;
    }


    /**
     * @param PushNotification $pushNotification
     * @return void
     */
    public final function onCreate(PushNotification $pushNotification)
    {
        $items = $this->ImportPushNotificationItems($pushNotification);
        foreach ($items as $item) {
            $item->save();
        }
        $customers  = $this->prepareAndSaveCustomers($pushNotification);
        if(is_null($customers)) {
            $this->alert("error", "Unable to create push notification because there are zero customers found in the customer group or type you selected?");
        }
        $pushNotification->push_notification_customers()->saveMany($customers);
        $this->pushNotification = NULL;
    }


    /**
     * @param PushNotification $pushNotification
     * @return PushNotificationCustomer[]|mixed[]
     */
    public final function prepareAndSaveCustomers(PushNotification $pushNotification) : array
    {
        $userModel = match ($pushNotification->app->model_id){
            6 => SupermarketUser::class,
            default=>WholesalesUser::class,
        };

        if(is_null($pushNotification->customer_group_id) and is_null($pushNotification->customer_type_id)) {
            $customers = $userModel::select('id', 'device_key')->whereNotNull('device_key')->get()->toArray();
            return array_map(function($customer) use ($userModel){
                return new PushNotificationCustomer([
                    'device_key' => $customer['device_key'],
                    'customer_type' => $userModel,
                    'customer_id' => $customer['id'],
                    'status_id' => status('Pending'),
                ]);
            }, $customers);

        } else {
            $typedCustomer = [];
            $groupedCustomer = [];
            if(!is_null($pushNotification->customer_group_id)) {
                $groupedCustomer = $userModel::select('id', 'device_key')->where('customer_group_id', $pushNotification->customer_group_id)->whereNotNull('device_key')->get()->toArray();
            }

            if(!is_null($pushNotification->customer_type_id)) {
                $typedCustomer = $userModel::select('id', 'device_key')->where('customer_type_id', $pushNotification->customer_type_id)->whereNotNull('device_key')->get()->toArray();
            }

            $customers = array_merge($typedCustomer, $groupedCustomer);

            $cachedCustomerID = [];
            $customers = array_filter($customers, function($customer) use (&$cachedCustomerID) {
                if(!in_array($customer['id'], $cachedCustomerID)) {
                    return $customer;
                }
                return false;
            });

            return array_map(function ($customer) use ($userModel){
                return new PushNotificationCustomer([
                    'device_key' => $customer['device_key'],
                    'customer_type' => $userModel,
                    'customer_id' => $customer['id'],
                    'status_id' => status('Pending'),
                ]);
            }, $customers);
        }

    }
}
