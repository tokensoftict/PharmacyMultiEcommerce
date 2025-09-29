<?php

namespace App\Livewire\Backend\Admin\Customer\Wholesales;

use App\Classes\ApplicationEnvironment;
use App\Models\SupermarketUser;
use App\Models\WholesalesUser;
use App\Models\WholessalesStockPrice;
use App\Services\User\Wholesales\WholeSalesCustomerService;
use Livewire\Component;

class ShowCustomer extends Component
{

    public WholesalesUser|SupermarketUser $wholesalesUser;
    public int $account;

    public array $customerData = [];

    public function mount()
    {
        if(ApplicationEnvironment::$stock_model == WholessalesStockPrice::class)
        {
            $this->wholesalesUser = WholesalesUser::find($this->account);
        } else {
            $this->wholesalesUser = SupermarketUser::find($this->account);
        }

        $this->customerData = [
            'phone' => $this->wholesalesUser->user->phone,
        ];
    }

    public function render()
    {
        return view('livewire.backend.admin.customer.wholesales.show-customer');
    }


    public function approveStore()
    {
        if($this->wholesalesUser instanceof WholesalesUser) {
            $service = app(WholeSalesCustomerService::class);
            $this->wholesalesUser = $service->activateBusiness($this->wholesalesUser);
            $this->alert("success", "Business has been activated and approved successfully");
        }
    }

    public function updateCustomer()
    {
        $this->wholesalesUser->user->phone = $this->customerData['phone'];
        $this->wholesalesUser->user->save();
        $this->dispatch("hideUpdateCustomerModal", []);
        $this->alert("success", "Phone Number has been updated successfully");
    }
}
