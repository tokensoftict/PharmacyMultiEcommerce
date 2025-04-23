<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\ApiController;
use App\Models\PaymentMethod;
use App\Services\Api\Checkout\ConfirmOrderService;
use App\Services\Api\Checkout\TransactionLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class PaymentGatewayTransactionLogsController extends ApiController
{
    protected ConfirmOrderService $confirmOrderService;

    /**
     * @param ConfirmOrderService $confirmOrderService
     */
    public function __construct(ConfirmOrderService $confirmOrderService)
    {
        $this->confirmOrderService = $confirmOrderService;
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
        $transactionLog = app(TransactionLogService::class);

        $log = $transactionLog->create([
            'user_id' => $checkoutUser->user->id,
            "gateway" => PaymentMethod::find($checkoutUser->getCheckoutPaymentMethod() ?? 0)->code,
            "total" => $totalOrderToPay,
            "email" => $checkoutUser->user->email,
            "phone" => $checkoutUser->user->phone,
            "currency" => "NGN"
        ]);

        return $this->sendSuccessResponse([
            "reference" => $log->transaction_reference,
            "email" => $log->email,
            "amount" => $log->total,
        ]);
    }

}
