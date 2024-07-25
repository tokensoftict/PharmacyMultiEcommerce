<?php
namespace App\Traits;

use App\Exports\GeneralDataExport;
use Maatwebsite\Excel\Facades\Excel;
use Masmerise\Toaster\Toaster;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait DynamicDataTableExport
{
    public final function getExportColumns() : array
    {
        $columns = $this->columns();

        $titleColumn = [];

        foreach ($columns as $column)
        {
            if($column->getColumnTitle() == "No.") continue;

            if(in_array($column->getColumnTitle(), $titleColumn))  continue;

            $titleColumn[] = $column->getColumnTitle();
        }

        return $titleColumn;
    }

    public final function getExportFields() : array
    {
        $columns = $this->columns();

        $titleField = [];

        foreach ($columns as $column)
        {
            $titleField[] = $column->getColumnField();
        }

        return $titleField;
    }

    public final function export_selected() : BinaryFileResponse
    {
        $selected = $this->getSelected();

        if(count($selected) == 0)
        {
            Toaster::error("Please select at least One Record to Export");

        }else {

            $export = $this->prepareExport();

            $this->clearSelected();

            return Excel::download(new GeneralDataExport($export['data'], $export['headings']), $this->getTableName() . '.xlsx');
        }
    }


    public final function renderValue($column, $row) : string | null
    {
        if ($column->isLabel()) {
            $value = call_user_func($column->getLabelCallback(), $row, $column);

            if ($column->isHtml()) {
                return $value;
            }

            return $value;
        }

        $value = $column->getValue($row);


        if ($column->hasFormatter()) {
            $value = call_user_func($column->getFormatCallback(), $value, $row, $column);

            $value = strip_tags($value);

            if ($column->isHtml()) {
                return $value;
            }

            return $value;
        }

        return $value;
    }

    public final function export_all()
    {
        $this->clearSelected();

        $export = $this->prepareExport();

        return Excel::download(new GeneralDataExport( $export['data'],  $export['headings']), $this->getTableName().'.xlsx');
    }


    public final function prepareExport() : array
    {
        $data = [];
        $headings = [];
        $rows = $this->getExportBuilder();

        $rows->chunk(1000, function($rowws) use(&$data, &$headings) {
            foreach ($rowws as $row){
                $columns_to_value = [];
                foreach ($this->getColumns() as $column){
                    if($column->getTitle() == "Action") continue;
                    if(!in_array($column->getTitle(), $headings)) {
                        $headings[] = $column->getTitle();
                    }
                    $columns_to_value[$column->getTitle()] = $this->renderValue($column, $row);
                }
                $data[] = $columns_to_value;
            }
        });

        return ['data' => $data, 'headings' => $headings];
    }


    public final function bulkActions(): array
    {
        return [
            'export_all' => 'Export All  (XLS)',
        ];
    }
}
