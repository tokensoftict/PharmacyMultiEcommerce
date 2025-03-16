<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\ExportDataTableComponent;
use App\Classes\PermissionAttribute;
use App\Models\ImageGallery;
use App\Models\ProductBanner;
use App\Models\Productcategory;
use App\Models\Slider;
use App\Traits\DynamicDataTableExport;
use App\Traits\DynamicDataTableFormModal;
use App\Traits\SimpleDatatableComponentTrait;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class ImageGalleryComponentDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, DynamicDataTableExport, DynamicDataTableFormModal;

    protected $model = ImageGallery::class;
    public static String $permissionComponentName = 'image_gallery';

    public function __construct()
    {
        $this->rowAction = ['edit', 'destroy'];

        $this->actionPermission = [
            'edit' => 'backend.admin.settings.image_gallery.update',
            'destroy' => 'backend.admin.settings.image_gallery.destroy',
            'create'   => 'backend.admin.settings.image_gallery.create',
            'status' => 'backend.admin.settings.image_gallery.toggle',
        ];

        $this->breadcrumbs = [
            [
                'route' => route('admin.dashboard'),
                'name' => "Dashboard",
                'active' =>false
            ],
            [
                'name' => "Image Galleries",
                'active' =>true
            ]
        ];


        $this->pageHeaderTitle = "Image Galleries";

        $this->extraRowAction = [];

        $this->rowSpinner = [
            [
                'label' => 'Status',
                'field' => 'status'
            ]
        ];
    }


    public function builder(): Builder
    {
        return ImageGallery::query();
    }


    public function mount()
    {
        $this->modalName = "Image Gallery";

        $this->data = [
            'title' => ['label' => 'Title', 'type'=>'text'],
            'image' => ['label' => 'Image', 'type'=>'image'],
        ];

        $this->newValidateRules = [
            'name' => 'required|min:3',
            'image' => 'required|min:3'
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Title", "title")
                ->sortable(),
            Column::make('image', 'image')
            ->format(fn($value, $row, Column $column) => '<div class="img-group"><img width="53" class="d-block border border-translucent rounded-2" src="'.$value.'"/></div>')->html(),
        ];
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.image_gallery')]
    public function view()
    {
    }
}
