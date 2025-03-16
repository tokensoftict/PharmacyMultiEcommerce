<?php

namespace App\Livewire\Backend\Admin\PushNotification;

use App\Models\PushNotification;
use Livewire\Component;

class ShowPushNotification extends Component
{

    public PushNotification $pushNotification;

    public array $notificationStatus = [
        'DRAFT' => 'info',
        'APPROVED' => 'primary',
        'SENT' => 'success',
        'CANCEL' => 'danger',
    ];

    public function render()
    {
        return view('livewire.backend.admin.push-notification.show-push-notification');
    }


    public function approve() : void
    {
        $this->pushNotification->status = 'APPROVED';
        $this->pushNotification->save();

        $this->dispatch('refreshPage');
        $this->alert(
            "success",
            "Push Notification Approved successfully!",
        );
    }


    public function cancel() : void
    {
        $this->pushNotification->status = 'CANCEL';
        $this->pushNotification->save();

        $this->dispatch('refreshPage');
        $this->alert(
            "success",
            "Push Notification Cancelled successfully!",
        );
    }

}
