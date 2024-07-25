<?php

namespace App\Livewire\Guest;

use Livewire\Component;

class Homepage extends Component
{
    public function render()
    {
        return view('livewire.guest.homepage')->layout('layout.main');
    }
}
