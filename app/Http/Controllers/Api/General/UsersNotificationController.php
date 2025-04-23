<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\General\UsersNotificationResource;
use App\Models\PushNotificationCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class UsersNotificationController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $checkoutUser = getApplicationModel();
        $notifications = PushNotificationCustomer::query()
            ->with('push_notification')
            ->where('customer_id', $checkoutUser->id)
            ->where('customer_type', get_class($checkoutUser))
            ->whereIn('status_id', [status('Pending'), status('Dispatched'), status('Complete')])
            ->orderBy('created_at', 'desc')
            ->limit(50)->get()
            ->groupBy(function ($item) {
                return carbonize($item['created_at'])->format('F jS, Y');
            })->map(function ($item, $index) {
                $notification =[];
                foreach($item as $noti) {
                    $notification[] =[
                        'description' => $noti->push_notification->body,
                        'title' => $noti->push_notification->title,
                        'state' => 'new_service'
                    ];
                }
                return [
                    'id' => mt_rand(),
                    'date' => $index,
                    'notifications' => $notification
                ];
            })->toArray();
        $notifications = collect(array_values($notifications));
        return $this->sendSuccessResponse(UsersNotificationResource::collection($notifications));
    }

}

/*
 *
 */