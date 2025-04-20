<?php

namespace App\Livewire\Backend\Admin\MedReminder;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Models\MedReminder;
use App\Models\Order;
use App\Models\WholesalesUser;
use App\Models\WholessalesStockPrice;
use App\Services\Api\MedReminder\MedReminderService;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MedReminderDataTableComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = MedReminder::class;

    public static String $permissionComponentName = 'medreminder';

    public function __construct()
    {
        $this->rowAction = [ 'destroy'];

        $this->actionPermission = [
            'create_med_reminder'=> 'backend.admin.med_reminder.create',
            'destroy' => 'backend.admin.med_reminder.destroy',
            'push' => 'backend.admin.med_reminder.push',
        ];

        $this->toolbarButtons = [
            'backend/component/medreminder/createmedreminder' =>[
                'label' => 'New Med Reminder',
                'type' => 'component',
                'route' => "backend.admin.med_reminder.create",
                'permission' => 'create_med_reminder',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-plus',
                'component' => 'backend.component.medreminder.createmedreminder',
                'is' => 'modal',
                'modal' => 'backend.component.medreminder.createmedreminder',
                'parameters' =>[]
            ]
        ];

        $this->extraRowAction = [];

        $this->extraRowActionButton = [
            [
                'label' => 'Push',
                'type' => 'method',
                'method' => 're_push',
                'route' => "backend.admin.customer_manager.wholesales.view",
                'permission' => 'push',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-refresh',
            ]
        ];

        $this->pageHeaderTitle  = "Med Reminder Lists";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "MedReminder Lists",
                'active' =>true
            ]
        ];

    }

    public function builder(): Builder
    {
        return MedReminder::query()->with(['user', 'stock']);
    }


    public function mount()
    {
        $this->modalName = "MedReminder";
        $this->data = [];
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Firstname", "user.firstname")
                ->searchable()
                ->sortable(),
            Column::make("Lastname", "user.lastname")
                ->searchable()
                ->sortable(),
            Column::make("Stock", "stock.name")
                ->searchable()
                ->sortable(),
            Column::make("Total Dosage", "total_dosage_in_package")
                ->searchable()
                ->sortable(),
            Column::make("Dosage", "dosage")
                ->searchable()
                ->sortable(),
            Column::make("Total Taken", "total_dosage_taken")
                ->searchable()
                ->sortable(),
            Column::make("Type", "type")
                ->searchable()
                ->sortable(),
            Column::make("Start Date", "start_date_time")
                ->format(function($value, $row, Column $column){
                    return carbonize($value)->format('m/d/Y');
                })
                ->searchable()
                ->sortable(),

        ];
    }

    public final function re_push(MedReminder $medReminder)
    {
        LivewireAlert::title("Med Reminder Push Notification")
            ->text("Are you sure you want to push this med reminder schedules to user's phone")
            ->withConfirmButton('Yes Please')
            ->withCancelButton('No Thanks')
            ->onConfirm('pushMedToDevice', ['id' => $medReminder->id])
            ->timer(0)
            ->show();
    }


    public final function pushMedToDevice(MedReminder $medReminder)
    {
        (new MedReminderService())->pushSchedulesToUsersPhone($medReminder);
    }

}

