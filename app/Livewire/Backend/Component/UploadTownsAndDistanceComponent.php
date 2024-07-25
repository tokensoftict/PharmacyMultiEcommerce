<?php

namespace App\Livewire\Backend\Component;

use App\Exports\ExportTownDistance;
use App\Imports\ImportTownDistance;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class UploadTownsAndDistanceComponent extends Component
{
    use WithFileUploads;

    public $file;


    public function render()
    {
        return view('livewire.backend.component.upload-towns-and-distance-component');
    }


    public function openModal()
    {
        $this->dispatch('openUploadTownsAndDistance');
    }


    public function downloadTownAndDeliveryTemplate()
    {
        return Excel::download(new ExportTownDistance, 'delivery-town-distance-'.todaysDate().'.xlsx');
    }

    public function uploadTownsAndDistance()
    {
        if(!$this->file){
            $this->alert(
                "error",
                "Please Upload or Select a File",
            );
            return false;
        }

        Excel::import(new ImportTownDistance(), $this->file);

        $this->alert(
            "success",
            "Operation Successful",
        );

        $this->dispatch('closeUploadTownsAndDistance', ['status' => true]);

    }
}
