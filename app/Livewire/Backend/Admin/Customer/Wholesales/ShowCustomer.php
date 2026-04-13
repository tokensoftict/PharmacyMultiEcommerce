<?php

namespace App\Livewire\Backend\Admin\Customer\Wholesales;

use App\Classes\ApplicationEnvironment;
use App\Models\SupermarketUser;
use App\Models\WholesalesUser;
use App\Models\WholessalesStockPrice;
use App\Services\User\Wholesales\WholeSalesCustomerService;
use App\Models\PushNotificationCustomer;
use App\Notifications\DevicePushNotification;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
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
            LivewireAlert::title("Success")->text("Business has been activated and approved successfully")->show();
        }
    }

    public function updateCustomer()
    {
        $this->wholesalesUser->user->phone = $this->customerData['phone'];
        $this->wholesalesUser->user->save();
        $this->dispatch("hideUpdateCustomerModal", []);
        LivewireAlert::title("Success")->text("Phone Number has been updated successfully")->show();
    }

    public function resendNotification($notificationCustomerId)
    {
        $notificationCustomer = PushNotificationCustomer::find($notificationCustomerId);

        if (!$notificationCustomer) {
            LivewireAlert::title("Error")->text("Notification record not found.")->show();
            return;
        }

        if (!$notificationCustomer->customer->device_key) {
            LivewireAlert::title("Error")->text("Customer has no registered device to receive push notifications.")->show();
            return;
        }

        try {
            $notificationCustomer->customer->notify(new DevicePushNotification($notificationCustomer->push_notification, $notificationCustomer));
            $notificationCustomer->status_id = status('Dispatched');
            $notificationCustomer->save();

            LivewireAlert::title("Success")->text("Notification has been resent successfully!")->show();
        } catch (\Exception $e) {
            LivewireAlert::title("Error")->text("Failed to resend notification: " . $e->getMessage())->show();
        }
    }
}
