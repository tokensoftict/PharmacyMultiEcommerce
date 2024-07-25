<?php
namespace App\Traits;

use App\Classes\Column;
use App\Classes\Settings;
use App\Exports\GeneralDataExport;
use App\Models\Usergroup;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Maatwebsite\Excel\Facades\Excel;

trait SimpleDatatableComponentTrait
{

    public  array $rowAction = [];

    public  array $rowSpinner = [];
    public array $extraRowActionButton = [];

    public array $extraRowAction = [];

    public array $breadcrumbs;

    public String $pageHeaderTitle;
    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setQueryStringDisabled()
            ->setOfflineIndicatorEnabled()
            ->setConfigurableAreas([
                'toolbar-right-start' => 'livewire.shared.create_button',
            ])
            ->setLayout('layout.app')
            ->setEmptyMessage('No Data found..')
            ->setTableAttributes(['class' => '',]);
    }


    public function columns(): array
    {
        $this->index = $this->getPage() > 1 ? ($this->getPage() - 1) * $this->getPage() : 0;

        $columns =  [
            Column::make('No.','id')->format(fn () => ++$this->index),
            ...self::mountColumn(),
        ];

        $rowSpinnerAction = [];
        $rowButtonAction = [];

        if(method_exists($this, 'rowSpinnerAction'))
        {
            $rowSpinnerAction = $this->rowSpinnerAction();
        }

        if(method_exists($this, 'rowButtonAction'))
        {
            $rowButtonAction = $this->rowButtonAction();
        }

        return array_merge($columns, $rowSpinnerAction, $rowButtonAction);
    }



}
