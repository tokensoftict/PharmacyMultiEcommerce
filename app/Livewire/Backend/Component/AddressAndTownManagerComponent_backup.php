<?php

namespace App\Livewire\Backend\Component;

use App\Classes\PermissionAttribute;
use App\Models\LocalGovt;
use App\Models\State;
use Livewire\Component;

class AddressAndTownManagerComponent_backup extends Component
{

    public  $states = null;
    public $lgas = null;

    public string $state_id = "";
    public string $lga_id = "";

    public function boot()
    {
        $this->states = State::select('name', 'id')->where('country_id', config('app.DEFAULT_COUNTRY_ID'))->get();
    }

    public function render()
    {
        return view('livewire.backend.settings.address-and-town-manager-component');
    }


    #[PermissionAttribute('View', 'view', 'backend.admin.settings.address_and_town')]
    public function view()
    {
    }

    public function fetchLGA()
    {
        $this->lgas = LocalGovt::select('id', 'name')->where('state_id', $this->state_id)->get();
    }

    public function filterTown()
    {

    }

}
