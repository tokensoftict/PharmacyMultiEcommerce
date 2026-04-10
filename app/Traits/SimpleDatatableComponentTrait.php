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

    public String $filterTable;

    public array $filter = [];
    public String $filterResetLink = "";
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
            ->setTableAttributes([
                'class' => 'table-premium table-hover align-middle',
            ])
            ->setThAttributes(function(Column $column) {
                return [
                    'class' => 'py-3 px-4 text-uppercase fw-bold text-nowrap',
                    'style' => 'font-size: 0.85rem; letter-spacing: 0.05em;',
                ];
            })
            ->setTdAttributes(function(Column $column, $row, $columnIndex, $rowIndex) {
                return [
                    'class' => 'py-3 px-4',
                ];
            });
    }


    public function columns(): array
    {
        $perPage = $this->getPerPage() ?? 25;
        $this->index = ($this->getPage() - 1) * $perPage;

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
