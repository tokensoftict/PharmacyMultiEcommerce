<?php

namespace App\Livewire\Backend\Admin\Campaign;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Enums\CampaignEvent;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\Concerns\SweetAlert2 as LivewireAlert;
use Rappasoft\LaravelLivewireTables\Views\Column;

/**
 * CampaignDatatable
 *
 * Admin list view for all campaigns. Provides search, status filter,
 * create/edit links, toggle active/paused, and analytics link.
 */
class CampaignDatatable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal, LivewireAlert;

    protected $model = Campaign::class;

    public function __construct()
    {
        $this->rowAction = ['edit'];

        $this->actionPermission = [
            'edit'    => 'backend.admin.campaign.edit',
            'destroy' => 'backend.admin.campaign.edit',
            'create'  => 'backend.admin.campaign.create',
            'view'    => 'backend.admin.campaign.analytics',
        ];

        $this->extraRowAction = ['view', 'toggle'];

        $this->extraRowActionButton = [
            [
                'label'      => 'Analytics',
                'type'       => 'link',
                'route'      => 'backend.admin.campaign.analytics',
                'permission' => 'view',
                'class'      => 'btn btn-sm btn-outline-info',
                'icon'       => 'fa fa-bar-chart',
            ],
        ];

        $this->pageHeaderTitle = 'Campaign Manager';

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix . 'admin.dashboard'),
                'name'  => 'Dashboard',
                'active' => false,
            ],
            [
                'name'   => 'Campaign Manager',
                'active' => true,
            ],
        ];
    }

    public function mount(): void
    {
        $this->modalName = 'Campaign';

        $this->data = [
            'name'             => ['type' => 'text',     'label' => 'Name',            'required' => true],
            'status'           => ['type' => 'select',   'label' => 'Status',          'required' => true,
                                   'options' => array_map(fn($s) => ['id' => $s, 'name' => ucfirst($s)], CampaignStatus::all())],
            'trigger_event'    => ['type' => 'select',   'label' => 'Trigger Event',   'required' => true,
                                   'options' => array_map(fn($e) => ['id' => $e, 'name' => $e], CampaignEvent::all())],
            'delivery_channel' => ['type' => 'select',   'label' => 'Delivery Channel', 'required' => true,
                                   'options' => [
                                       ['id' => 'in_app', 'name' => 'In-App'],
                                       ['id' => 'push', 'name' => 'Push'],
                                       ['id' => 'both', 'name' => 'Both'],
                                   ]],
            'store_type'       => ['type' => 'select',   'label' => 'Store Type',      'required' => true,
                                   'options' => [
                                       ['id' => 'both', 'name' => 'Both'],
                                       ['id' => 'retail', 'name' => 'Retail'],
                                       ['id' => 'wholesale', 'name' => 'Wholesale'],
                                   ]],
            'starts_at'        => ['type' => 'datetime-local', 'label' => 'Starts At', 'required' => false],
            'ends_at'          => ['type' => 'datetime-local', 'label' => 'Ends At',   'required' => false],
        ];

        $this->newValidateRules    = ['data.name' => ['required', 'string', 'min:3']];
        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }

    public function builder(): Builder
    {
        return Campaign::query()
            ->withTrashed(false)
            ->orderByDesc('priority')
            ->orderByDesc('created_at');
    }

    public static function mountColumn(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Status', 'status')
                ->format(function ($value) {
                    $map = [
                        'draft'    => 'secondary',
                        'active'   => 'success',
                        'paused'   => 'warning',
                        'expired'  => 'danger',
                        'archived' => 'dark',
                    ];
                    return label(ucfirst($value), $map[$value] ?? 'secondary');
                })->html()->sortable(),
            Column::make('Trigger', 'trigger_event')->sortable(),
            Column::make('Channel', 'delivery_channel')->sortable(),
            Column::make('Store', 'store_type')->sortable(),
            Column::make('Impressions', 'total_impressions')->sortable(),
            Column::make('Clicks', 'total_clicks')->sortable(),
            Column::make('Push Sent', 'total_push_sent')->sortable(),
            Column::make('Starts At', 'starts_at')->sortable(),
            Column::make('Ends At', 'ends_at')->sortable(),
        ];
    }

    /**
     * Toggle campaign active ↔ paused from the list view.
     */
    public function toggleStatus(Campaign $campaign): void
    {
        if ($campaign->status === CampaignStatus::ACTIVE) {
            $campaign->status = CampaignStatus::PAUSED;
            $this->alert('info', 'Campaign paused.');
        } else {
            $campaign->status = CampaignStatus::ACTIVE;
            $this->alert('success', 'Campaign activated.');
        }
        $campaign->save();
        $this->dispatch('refreshPage');
    }
}
