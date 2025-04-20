<?php

namespace App\Livewire\Backend\Admin\Administrator;

use App\Classes\ApplicationEnvironment;
use App\Classes\BooleanColumn;
use App\Classes\ExportDataTableComponent;
use App\Mail\Administrator\AdministratorInvitationMail;
use App\Models\CustomerSearchHistory;
use App\Models\SalesRepresentative;
use App\Models\SupermarketAdmin;
use App\Models\WholesalesAdmin;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Mail;
use Rappasoft\LaravelLivewireTables\Views\Column;


class AdministratorManagerDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = "";


    public function __construct()
    {
        $this->model = ApplicationEnvironment::$appModel;

        $this->extraRowAction = ['view'];


        $this->toolbarButtons = [
            'backend/component/administrator/createadministrator' =>[
                'label' => 'New Administrator',
                'type' => 'component',
                'route' => "backend.admin.user.create",
                'permission' => 'create_new_administrator',
                'class' => 'btn btn-sm btn-outline-success',
                'icon' => 'fa fa-plus',
                'component' => 'backend.component.administrator.createadministrator',
                'is' => 'modal',
                'modal' => 'backend.component.administrator.createadministrator',
                'parameters' =>[]
            ]
        ];

        $this->actionPermission = [
            'create_new_administrator'=> 'backend.admin.user.create',
            're_send_invitation' => "backend.admin.user.resend_invitation",
            'status' => 'backend.admin.user.toggle',
        ];


        $this->extraRowActionButton = [
            [
                'label' => 'Resend Invite',
                'type' => 'method',
                'method' => 're_send_invitation',
                'route' => "backend.admin.user.resend_invitation",
                'permission' => 're_send_invitation',
                'class' => 'btn btn-sm btn-primary',
                'icon' => 'fa fa-refresh',
                'visible' => 'can_send_invitation',
            ],
        ];

        $this->rowSpinner = [
            [
                'label' => 'Account Status  ',
                'field' => 'status'
            ]
        ];


        $this->pageHeaderTitle = "Administrator List";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Administrator List",
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
        return ApplicationEnvironment::$appModel::query()->with(['user']);
    }

    /**
     * @return array
     */
    public static function  mountColumn() : array
    {
        return [
            Column::make("First Name", "user.firstname")
                ->searchable()
                ->sortable(),
            Column::make("Last Name", "user.lastname")
                ->searchable()
                ->sortable(),
            Column::make("Email Address", "user.email")
                ->searchable()
                ->sortable(),
            Column::make("Phone Number", "user.phone")
                ->searchable()
                ->sortable(),
            BooleanColumn::make("Invitation Status", "invitation_status")
                ->sortable(),
            Column::make("Invitation Date", "invitation_sent_date")
                ->format(function($value, $row, Column $column){
                    return $value  ? $value->format('l, F jS, Y') : "";
                })
                ->sortable(),
            Column::make("Accepted Date", "invitation_approval_date")
                ->format(function($value, $row, Column $column){
                    return $value  ? $value->format('l, F jS, Y') : "";
                })
                ->sortable(),
        ];
    }


    public function re_send_invitation(array|SupermarketAdmin|WholesalesAdmin $user)
    {
        $user = ApplicationEnvironment::$appModel::find($user['id']);
        $user->token = sha1(md5(generateRandomString(50)));
        $user->save();
        $user->fresh();
        $link = route('administrator.admin.accept-invitation', $user->token);
        Mail::to($user->user->email)->send(new AdministratorInvitationMail($user, $link));
        $this->alert("success", 'An Invitation Email has been sent to ' . $user->email . " " . $user->name . " will become an administrator when they accept the invite  &#128513;");
    }


    public function can_send_invitation(array|SupermarketAdmin|WholesalesAdmin $user)
    {
        if($user->status == "0") return true;
        return false;
    }
}
