<?php

namespace App\Livewire\Backend\Component;

use App\Exports\StockSizesExport;
use App\Imports\ImportTownDistance;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class UploadStockSizeComponent extends Component
{
    use WithFileUploads;

    public $file;

    public function render()
    {
        return view('livewire.backend.component.upload-stock-size-component');
    }

    public function openModal()
    {
        $this->dispatch('openUploadStockSize');
    }


    public function downloadStockSize()
    {
        return Excel::download(new StockSizesExport, 'stock-size-export-'.todaysDate().'.xlsx');
    }


    public function uploadStockSize()
    {
        if(!$this->file){

            $this->alert(
                "error",
                "Please Upload or Select a File",
            );
            return false;
        }

        Excel::import(new ImportTownDistance, $this->file);

        $this->dispatch('closeUploadStockSize', ['status' => true]);

        $this->alert(
            "success",
            "Operation Successful",
        );

    }

}
