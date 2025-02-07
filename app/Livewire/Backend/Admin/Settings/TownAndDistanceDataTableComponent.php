<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Livewire\Backend\Component\UploadTownsAndDistanceComponent;
use App\Models\DeliveryTownDistance;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TownAndDistanceDataTableComponent extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = DeliveryTownDistance::class;

    public static String $permissionComponentName = 'town_and_distance';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.town_and_distance.update',
            'destroy' => 'backend.admin.settings.town_and_distance.destroy',
            'create'   => 'backend.admin.settings.town_and_distance.create',
            'upload_towns_distance'=> 'backend.admin.settings.town_and_distance.upload'
        ];

        $this->extraRowAction = [];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Town And Distance",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Town And Distance";

        $this->toolbarButtons = [
            UploadTownsAndDistanceComponent::class => [
                  'label' => 'Upload Towns & Distance',
                  'type' => 'component',
                  'route' => "backend.admin.settings.town_and_distance.upload",
                  'permission' => 'upload_towns_distance',
                  'class' => 'btn btn-sm btn-outline-primary',
                  'icon' => 'fa fa-cloud',
                  'component' => UploadTownsAndDistanceComponent::class,
                  'is' => 'modal',
                  'modal' => 'pages.backend.admin.component.upload-towns-and-distance-component',
                  'parameters' =>[]
              ]
        ];

    }

    public function builder(): Builder
    {
        return DeliveryTownDistance::query()->with(['town', 'town.state', 'town.local_govt']);
    }


    public function mount()
    {
        $this->modalName = "Towns & Distance";

        $this->data = [
            'town_id' => ['label' => 'Town', 'type'=>'select', 'placeholder' => 'Select Town', 'options'=> towns()->toArray()],
            'town_distance' => ['label' => 'Distance', 'type'=>'text'],
            'no' => ['label' => 'No', 'type'=>'text'],
            'frequency' => ['label' => 'Frequency', 'type'=>'select', 'placeholder' => 'Select Frequency',
                'options' => [
                    [
                        'name' => 'Day',
                        'id' => 'days'
                    ],
                    [
                        'name' => 'Week',
                        'id' => 'week'
                    ],
                    [
                        'name' => 'Month',
                        'id' => 'month'
                    ],
                    [
                        'name' => 'Year',
                        'id' => 'year'
                    ]
                ]
            ],
            'minimum_shipping_amount' => ['label' => 'Minimum Delivery Cost', 'type'=>'text'],
            'fixed_shipping_amount' => ['label' => 'Fixed Delivery Cost', 'type'=>'text'],
            'delivery_days' => ['label' => 'Delivery Days', 'type'=>'select', 'multiple'=>true,
                'options' => [
                    [
                        'name' => 'Monday',
                        'id' => 'Monday'
                    ],
                    [
                        'name' => 'Tuesday',
                        'id' => 'Tuesday'
                    ],
                    [
                        'name' => 'Wednesday',
                        'id' => 'Wednesday'
                    ],
                    [
                        'name' => 'Thursday',
                        'id' => 'Thursday'
                    ],
                    [
                        'name' => 'Friday',
                        'id' => 'Friday'
                    ],
                    [
                        'name' => 'Saturday',
                        'id' => 'Saturday'
                    ],
                    [
                        'name' => 'Sunday',
                        'id' => 'Sunday'
                    ],
                ]
            ],
        ];

        $this->newValidateRules = [
            'town_id' => 'required',
            'town_distance' => 'required',
            'no' => 'required',
            'frequency' => 'required',
            'minimum_shipping_amount' => 'required',
            'fixed_shipping_amount' => 'required',
            'delivery_days' => 'required',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }

    public static function  mountColumn() : array
    {
        return [
            Column::make("Town Name", "town_id")
                ->format(function($value, $row, Column $column){
                    return $row->town->name;
                })
                ->sortable(),
            Column::make("State", "town_id")
                ->format(function($value, $row, Column $column){
                    return $row->town->state->name;
                }),
            Column::make("Local Govt", "town_id")
                ->format(function($value, $row, Column $column){
                    return $row->town->local_govt->name;
                }),
            Column::make("Distance", "town_distance")
                ->sortable(),
            Column::make("No", "no")
                ->sortable(),
            Column::make("Frequency", "frequency")
                ->sortable(),
            Column::make("Minimum Delivery Cost", "minimum_shipping_amount")
                ->sortable(),
            Column::make("Fixed Delivery Cost", "fixed_shipping_amount")
                ->sortable(),
            /*
            Column::make("Delivery Days", "delivery_days")
                ->format(function($value, $row, Column $column){
                    return implode(",", $value);
                })->sortable(),
            */
        ];
    }


}
