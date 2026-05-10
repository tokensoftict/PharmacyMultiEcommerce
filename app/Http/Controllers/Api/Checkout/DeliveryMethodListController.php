<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Classes\ApplicationEnvironment;
use App\Http\Controllers\ApiController;
use App\Models\DeliveryMethod;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryMethodListController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $deliveryMethods = DeliveryMethod::where("app_id", ApplicationEnvironment::$id)->get();

        $deliveryMethods->each(function ($method) {
            if ($method->code === 'Dwi' || $method->code === 'DI-ILN') {
                $locations = \App\Models\DeliveryWithinIlorin::where('app_id', ApplicationEnvironment::$id)
                    ->where('status', true)
                    ->get()
                    ->map(function ($loc) use ($method) {
                        return [
                            'SN' => $loc->id,
                            'name' => $loc->name,
                            'amount' => $loc->amount,
                            'original_amount' => $loc->amount
                        ];
                    })->toArray();
                $method->template_settings_value = $locations;
            }

            if ($method->isFreeDeliveryActive()) {
                if ($method->template_settings_value) {
                    $settings = $method->template_settings_value;
                    foreach ($settings as &$setting) {
                        if (isset($setting['amount'])) {
                            $setting['original_amount'] = $setting['amount'];
                            $setting['amount'] = 0;
                        }
                        if (isset($setting['price'])) {
                            $setting['original_price'] = $setting['price'];
                            $setting['price'] = 0;
                        }
                    }
                    $method->template_settings_value = $settings;
                    $method->is_free = true;
                }
            }
        });

        return $this->showAll($deliveryMethods);
    }

}
