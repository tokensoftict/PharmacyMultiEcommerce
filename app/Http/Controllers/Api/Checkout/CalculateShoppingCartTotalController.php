<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Http\Controllers\ApiController;
use App\Services\Api\Checkout\ConfirmOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CalculateShoppingCartTotalController extends ApiController
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
        $confirmOrder = $this->confirmOrderService->confirmOrderReturnAnalysis(false);

        if($confirmOrder['status'] === false) {
            return $this->sendErrorResponse($confirmOrder['message'], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->sendSuccessResponse($confirmOrder['confirmOrder']);
    }



}
