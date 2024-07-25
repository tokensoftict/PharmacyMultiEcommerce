<?php

namespace App\Livewire\Backend\Component;

use App\Exports\StockRestrictionTemplate;
use App\Imports\StockRestrictionImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class StockRestrictionComponent extends Component
{
    use WithFileUploads;

    public array $parameters = [];

    public $file;

    public function render()
    {
        return view('livewire.backend.component.stock-restriction-component');
    }

    public function downloadRestrictionTemplate()
    {
        return Excel::download(new StockRestrictionTemplate($this->parameters), 'stock-restriction-template-'.todaysDate().'.xlsx');
    }

    public function uploadRestriction()
    {

        if(!$this->file){
            $this->alert(
                "error",
                "Please Upload or Select a File",
            );
            return false;
        }

        Excel::import(new StockRestrictionImport($this->parameters), $this->file);
        $this->alert(
            "success",
            "Operation Successful"

        );

        $this->dispatch('closeRestrictionModal', ['status' => true]);
    }
}
