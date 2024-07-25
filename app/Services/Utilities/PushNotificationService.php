<?php

namespace App\Services\Utilities;

use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Support\Arr;

class PushNotificationService
{
    /**
     * @var PushNotification
     */
    private PushNotification $pushNotification;


    public function __construct(PushNotification|null $pushNotification =  null)
    {
        if(!is_null($pushNotification)){
            $this->pushNotification = $pushNotification;
        }
    }


    /**
     * @param array $data
     * @return $this
     */
    public final function createNotification(array $data) : PushNotificationService
    {
        $data = Arr::only($data, [
            "title",
            "body",
            "payload",
            "device_ids",
            "app_id",
            "action",
            "type",
            "status",
        ]);

        $data['user_id'] = request()?->user()?->id ?? User::selfSystem()->id;
        $this->pushNotification = PushNotification::create($data);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public final function update($data) : PushNotificationService
    {
        $data = Arr::only($data, [
            "title",
            "body",
            "payload",
            "device_ids",
            "app_id",
            "action",
            "type",
            "status",
        ]);

        $this->pushNotification->update($data);
        return $this;
    }

    /**
     * @return $this
     */
    public final function approved() : PushNotificationService
    {
        $this->pushNotification->status = "APPROVED";
        $this->pushNotification->update();
        return $this;
    }


    /**
     * @return PushNotificationService
     */
    public final function cancel() : PushNotificationService
    {
        $this->pushNotification->status = "CANCEL";
        $this->pushNotification->update();
        return $this;
    }


    /**
     * @return $this]
     */
    public final function send() : PushNotificationService
    {
        if($this->pushNotification->status === "APPROVED"){

        }
       return $this;
    }
}
