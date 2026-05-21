<?php

namespace App\Livewire\Backend\Admin\PushNotification;

use App\Models\PushNotification;
use Livewire\Component;
use Livewire\WithPagination;

class ShowPushNotification extends Component
{
    use WithPagination;

    public PushNotification $pushNotification;
    public $search = '';

    protected $paginationTheme = 'bootstrap';

    public array $notificationStatus = [
        'DRAFT' => 'info',
        'APPROVED' => 'primary',
        'SENT' => 'success',
        'CANCEL' => 'danger',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = trim($this->search);

        $push_notification_customers = $this->pushNotification->push_notification_customers()
            ->with(['customer.user'])
            ->when($search, function ($query) use ($search) {
                $query->whereHasMorph('customer', [\App\Models\WholesalesUser::class, \App\Models\SupermarketUser::class], function ($q, $type) use ($search) {
                    $q->where(function ($innerQuery) use ($type, $search) {
                        if ($type === \App\Models\WholesalesUser::class) {
                            $innerQuery->where('business_name', 'like', '%' . $search . '%');
                        }
                    })->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
                });
            })
            ->paginate(20);

        return view('livewire.backend.admin.push-notification.show-push-notification', [
            'push_notification_customers' => $push_notification_customers
        ]);
    }


    public function approve() : void
    {
        $this->pushNotification->status = 'APPROVED';
        $this->pushNotification->save();

        sendNotificationToDevice($this->pushNotification);

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

    public function resend() : void
    {
        sendNotificationToDevice($this->pushNotification, [status('Dispatched'), status('Processing Error'), status('Complete')], false);

        $this->alert(
            "success",
            "Push Notification Resent successfully!",
        );
    }

}
