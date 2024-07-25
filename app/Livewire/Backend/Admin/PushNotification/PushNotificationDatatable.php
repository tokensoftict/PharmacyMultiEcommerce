<?php

namespace App\Livewire\Backend\Admin\PushNotification;

use App\Classes\ExportDataTableComponent;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Livewire\WithFileUploads;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PushNotification;
use Illuminate\Database\Eloquent\Builder;

class PushNotificationDatatable extends ExportDataTableComponent
{
    use WithFileUploads;

    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = PushNotification::class;


    public function __construct()
    {
        $this->rowAction = ['edit'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.customer_group.update',
            'destroy' => 'backend.admin.settings.customer_group.destroy',
            'create'   => 'backend.admin.settings.customer_group.create',
        ];


        $this->extraRowAction = [];

        $this->pageHeaderTitle = "Push Notification Lists";


        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
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
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }

    public function builder(): Builder
    {
        return PushNotification::query()->with(['user', 'app_user']);
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Title", "title")
                ->sortable(),
            Column::make("Body", "body")
                ->sortable(),
            Column::make("No of device", "no_of_device")
                ->sortable(),
            Column::make("Total view", "total_view")
                ->sortable(),
            Column::make("Total sent", "total_sent")
                ->sortable(),
            Column::make("Status", "status")
                ->sortable(),
            Column::make("Created", "user.name")
                ->sortable(),
            Column::make("Created at", "created_at")

        ];
    }


}
