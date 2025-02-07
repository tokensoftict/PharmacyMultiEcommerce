<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\ApiController;
use App\Models\PaymentMethod;
use App\Services\Api\Checkout\ConfirmOrderService;
use App\Services\Order\CreateOrderProductService;
use App\Services\Order\CreateOrderService;
use App\Services\Order\CreateOrderTotalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class ConfirmPaymentController extends ApiController
{
    protected ConfirmOrderService $confirmOrderService;
    protected CreateOrderService $createOrderService;
    protected CreateOrderProductService  $createOrderProductService;
    protected CreateOrderTotalService $createOrderTotalService;

    public function __construct(ConfirmOrderService $confirmOrderService, CreateOrderService $createOrderService, CreateOrderTotalService $createOrderTotalService, CreateOrderProductService  $createOrderProductService)
    {
        $this->confirmOrderService = $confirmOrderService;
        $this->createOrderService = $createOrderService;
        $this->createOrderProductService = $createOrderProductService;
        $this->createOrderTotalService = $createOrderTotalService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request) : JsonResponse
    {
        $checkoutUser = getApplicationModel();

        if(!$checkoutUser) {
            return $this->sendErrorResponse("Application user error, Please restart the application to complete your checkout", ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $totalOrderToPay = $this->confirmOrderService->confirmOrderReturnTotal();

        if(is_array($totalOrderToPay)) {
            return $this->sendErrorResponse($totalOrderToPay['message'], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $paymentMethod = PaymentMethod::findorfail($checkoutUser->getCheckoutPaymentMethod());
        $paymentRepository = 'App\\Repositories\\'.ucwords(strtolower($paymentMethod->code)).'Repository';
        $paymentRepository = new $paymentRepository();

        $confirmPayment = $paymentRepository->confirmPayment($paymentMethod, $request->all());
        if($confirmPayment['status'] === false) {
            return $this->sendErrorResponse($confirmPayment['message'], ResponseAlias::HTTP_BAD_REQUEST);
        }

        //payment was found to be successful, lets create order
        return DB::transaction(function() use ($request, $checkoutUser) {
            $order = $this->createOrderService->create([
                'comment' => $request->get('comment', " "),
                'address_id' => $checkoutUser->getCheckoutDeliveryMethod()['deliveryMethod'],
                'payment_method_id' => $checkoutUser->getCheckoutPaymentMethod(),
            ]);

            $order = $this->createOrderProductService->create($order);
            $order = $this->createOrderTotalService->create($order);
            $checkoutUser->prepareUserAccountForNewOrder();

            return $this->showOne(
                $order
            );
        });




    }
}
