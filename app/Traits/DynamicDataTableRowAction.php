<?php
namespace App\Traits;

use App\Classes\Column;

trait DynamicDataTableRowAction
{
    public $rowClass = '';
    private function rowButtonAction() : array
    {
        $action = [];
        $html = '';
        if((count($this->rowAction) > 0 || count($this->extraRowAction) > 0) && $this->hasAtleastOneExtraRowAccessPermission()) {

            $action [] = Column::make('Action', 'id')
                ->format(function ($value, $row, Column $column) use ($html) {

                    if ((
                        (
                            isset($this->actionPermission['edit']) &&
                            userCanView($this->actionPermission['edit'])
                        )
                        ||
                        (
                            isset($this->actionPermission['destroy']) &&
                            userCanView($this->actionPermission['destroy'])
                        )
                    )) {

                        if (in_array('edit', $this->rowAction) && userCanView($this->actionPermission['edit'])) {
                            $html .= '&nbsp;<a href="#" wire:click.prevent="edit(' . $row->id . ')" class="btn btn-sm  btn-phoenix-primary  ">
                        <i wire:loading.remove wire:target="edit(' . $row->id . ')" class="fa fa-pencil-alt"></i>
                        <span wire:loading wire:target="edit(' . $row->id . ')" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Edit
                    </a>';
                        }

                        if (in_array('destroy', $this->rowAction) && userCanView($this->actionPermission['destroy'])) {
                            $html .= '&nbsp;<a href="#" wire:confirm="Are you sure you want to delete this record?" wire:click.prevent="destroy(' . $row->id . ')" class="btn btn-sm btn-danger  ">
                        <i wire:loading.remove wire:target="destroy(' . $row->id . ')" class="fa fa-trash-alt"></i>
                        <span wire:loading wire:target="destroy(' . $row->id . ')" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Delete
                    </a>';
                        }

                    }
                    return $html.$this->extraRowAction($row);
                })
                ->html();
        }
        return $action;
    }


    private function hasAtleastOneExtraRowAccessPermission() : bool
    {
        foreach ($this->extraRowActionButton as $button){
            if(isset($this->actionPermission[$button['permission'] ?? ""]) && userCanView($this->actionPermission[$button['permission'] ?? ""])) return true;
        }

        if ((
            (
                isset($this->actionPermission['edit']) &&
                userCanView($this->actionPermission['edit'])
            )
            ||
            (
                isset($this->actionPermission['destroy']) &&
                userCanView($this->actionPermission['destroy'])
            )
        )){
            return true;
        }
        return false;
    }

    private function extraRowAction($row) : string
    {
        $extraButtonAction = '&nbsp;&nbsp;';

        foreach ($this->extraRowActionButton as $button){

            if($button['type'] == "link" && userCanView($this->actionPermission[$button['permission']])){
                if(isset($button['parameters'])){
                    $params = [];
                    foreach ($button['parameters'] as $key=>$value){
                        $params[$key] = $row->{$value};
                    }
                    $extraButtonAction .= '<a  href="' . route($button['route'],  $params) . '" class="' . ($button['class'] ?? 'btn btn-default') . '"><i class="'.$button['icon'].'"></i> ' . $button['label'] . '</a>';
                }else {
                    $extraButtonAction .= '<a  href="' . route($button['route'], $row->id) . '" class="' . ($button['class'] ?? 'btn btn-default') . '"><i class="'.$button['icon'].'"></i> ' . $button['label'] . '</a>';
                }
            }

            if($button['type'] == "component")
            {   $this->rowClass = get_class($row);
                if($button['is'] === 'modal') {
                    $extraButtonAction .= '<a  wire:click.prevent="openCustomModal('.$row.')" href="#'.$button['modal'].'" class="' . ($button['class'] ?? 'btn btn-default') . '"><i wire:loading.remove wire:target="openCustomModal(' .$row. ')" class="'.($button['icon'] ?? 'fa fa-trash-alt').'"></i><span wire:loading wire:target="openCustomModal(' .$row. ')" class="spinner-border spinner-border-sm me-2" role="status"></span>  ' . $button['label'] . '</a>';
                }
            }

            if($button['type'] == "method")
            {
                $method = $button['method'];
                $extraButtonAction .= '<a  wire:click.prevent="'.$method.'('.$row.')" href="#'.$button['method'].'" class="' . ($button['class'] ?? 'btn btn-default') . '"><i wire:loading.remove wire:target="'.$method.'(' .$row. ')" class="'.($button['icon'] ?? 'fa fa-trash-alt').'"></i><span wire:loading wire:target="'.$method.'(' .$row. ')" class="spinner-border spinner-border-sm me-2" role="status"></span>  ' . $button['label'] . '</a>';
            }
        }

        return $extraButtonAction;
    }

    private function rowSpinnerAction() : array
    {
        $rowSpinner = [];

        foreach ($this->rowSpinner as $spinner) {
            if(userCanView($this->actionPermission[$spinner['field']])) {
                $rowSpinner [] = Column::make($spinner['label'], $spinner['field'],)
                    ->format(function ($value, $row, Column $column) use ($spinner){
                        if(isset($spinner['handler'])){
                            return '<div class="form-check form-switch">
                                  <input wire:change="'.$spinner['handler'].'(' . $row->id . ')" class="form-check-input" id="customSwitch_' .$spinner['handler']. $row->id . '" ' . ($row->{$spinner['handler']} ? 'checked' : '') . ' type="checkbox" />
                                  <label class="form-check-label" for="customSwitch' .$spinner['handler']. $row->id . '"></label>
                                </div>';
                        } else {
                            return '<div class="form-check form-switch">
                                  <input wire:change="toggle(' . $row->id . ')" class="form-check-input" id="customSwitch' . $row->id . '" ' . ($row->status ? 'checked' : '') . ' type="checkbox" />
                                  <label class="form-check-label" for="customSwitch' . $row->id . '"></label>
                                </div>';
                        }
                    })->html();
            }
        }

        return $rowSpinner;
    }


    public function openCustomModal($row)
    {
        $this->dispatch('openRestrictionModal', ['type'=>$this->rowClass, 'row'=>$row]);
    }

}
