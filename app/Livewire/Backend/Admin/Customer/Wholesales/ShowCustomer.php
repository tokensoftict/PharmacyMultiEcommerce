<?php

namespace App\Livewire\Backend\Admin\Customer\Wholesales;

use App\Classes\ApplicationEnvironment;
use App\Models\SupermarketUser;
use App\Models\WholesalesUser;
use App\Models\WholessalesStockPrice;
use Livewire\Component;

class ShowCustomer extends Component
{

    public WholesalesUser|SupermarketUser $wholesalesUser;
    public int $account;

    public function mount()
    {
        if(ApplicationEnvironment::$stock_model == WholessalesStockPrice::class)
        {
            $this->wholesalesUser = WholesalesUser::find($this->account);
        } else {
            $this->wholesalesUser = SupermarketUser::find($this->account);
        }
    }

    public function render()
    {
        return view('livewire.backend.admin.customer.wholesales.show-customer');
    }


    public function approveStore()
    {
        
    }
}
