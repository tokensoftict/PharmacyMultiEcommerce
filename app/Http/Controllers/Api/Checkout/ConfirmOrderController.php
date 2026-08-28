<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\Api\Campaign\CartAbandonmentHookController;
use App\Http\Controllers\ApiController;
use App\Classes\ApplicationEnvironment;
use App\Services\Api\Checkout\ConfirmOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class ConfirmOrderController extends ApiController
{
    protected ConfirmOrderService $confirmOrderService;
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
        $confirmOrder = $this->confirmOrderService->confirmOrderReturnAnalysis();

        if($confirmOrder['status'] === false) {
            return response()->json([
                'status' => false,
                'message' => $confirmOrder['message'],
                'inventory_errors' => $confirmOrder['inventory_errors'] ?? []
            ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Campaign: reset cart abandonment tracker on successful order
        $storeType = ApplicationEnvironment::$stock_model_string === 'wholessales_stock_prices'
            ? 'wholesale'
            : 'retail';
        CartAbandonmentHookController::onOrderPlaced($request->user()->id, $storeType);

        return $this->sendSuccessResponse($confirmOrder['confirmOrder']);
    }



}
