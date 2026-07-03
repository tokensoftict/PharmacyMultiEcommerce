<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ApplicationEnvironment;
use App\Classes\ExportDataTableComponent;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class UserVersionReportComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = User::class;

    public function __construct()
    {
        $this->rowAction = [];
        $this->extraRowAction = [];
        $this->toolbarButtons = [];

        $this->pageHeaderTitle = "User Version Report";

        $this->breadcrumbs = [
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'admin.dashboard'),
                'name' => "Dashboard",
                'active' => false
            ],
            [
                'route' => route(ApplicationEnvironment::$storePrefix.'backend.admin.settings.app_update_settings'),
                'name' => "App Update Settings",
                'active' => false
            ],
            [
                'name' => "User Version Report",
                'active' => true
            ]
        ];
    }

    public function mount()
    {
        $this->data = [];
    }

    public function builder(): Builder
    {
        return User::query()
            ->whereNotNull('device_type')
            ->orderBy('last_seen', 'desc');
    }

    public static function mountColumn(): array
    {
        return [
            Column::make("First Name", "firstname")
                ->searchable()
                ->sortable(),
            Column::make("Last Name", "lastname")
                ->searchable()
                ->sortable(),
            Column::make("Email", "email")
                ->searchable()
                ->sortable(),
            Column::make("Phone", "phone")
                ->searchable()
                ->sortable(),
            Column::make("Device Type", "device_type")
                ->sortable(),
            Column::make("Version", "version")
                ->sortable(),
            Column::make("Version Code", "version_code")
                ->sortable(),
            Column::make("Last Seen", "last_seen")
                ->format(fn($value) => $value ? $value->format('Y-m-d H:i:s') : 'Never')
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Device Type')
                ->options([
                    '' => 'All',
                    'android' => 'Android',
                    'ios' => 'iOS',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('device_type', $value);
                    }
                }),
            TextFilter::make('Version')
                ->config([
                    'placeholder' => 'Search Version',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('version', 'like', '%' . $value . '%');
                }),
            TextFilter::make('Version Code')
                ->config([
                    'placeholder' => 'Search Version Code',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('version_code', $value);
                }),
        ];
    }
}
