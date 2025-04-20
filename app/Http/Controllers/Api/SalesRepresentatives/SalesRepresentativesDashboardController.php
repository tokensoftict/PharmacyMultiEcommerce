<?php

namespace App\Http\Controllers\Api\SalesRepresentatives;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Auth\WholesalesUserResource;
use App\Http\Resources\Api\Order\OrderSalesRepresentativeResource;
use App\Services\SalesRepresentative\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesRepresentativesDashboardController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public ReportService $reportService;

    public function __invoke(Request $request) : JsonResponse
    {
        $this->reportService = app(ReportService::class);
        $user = $request->user();
        $salesRep = $user->sales_representative;

        $this->reportService->setSalesRepresentative($salesRep);

        return $this->sendSuccessResponse([
            'totalNumberOfCustomers' => $this->reportService->getTotalNumberOfCustomers(),
            'totalNumberOfDispatchedOrders' => $this->reportService->geTotalOrderDispatchedCount(),
            'sumOfDispatchedOrders' => $this->reportService->geTotalOrderDispatchedSum(),
            'customerList' => WholesalesUserResource::collection($this->reportService->getCustomers()),
            'orderList' => OrderSalesRepresentativeResource::collection($this->reportService->getCustomerOrders()),
            'profile' => $this->reportService->getProfileInformation(),
            'month' => $this->reportService->month,
        ]);
    }

}
