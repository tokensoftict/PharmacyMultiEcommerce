<?php

namespace App\Http\Controllers\Api\Stock;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Api\Stock\StockShowResource;
use App\Models\Stockbarcode;
use App\Services\Stock\StockService;
use Illuminate\Http\JsonResponse;

class ScanController extends ApiController
{
    public StockService $service;

    public function __construct(StockService $service)
    {
        $this->service = $service;
    }

    public function scan(string $code): JsonResponse
    {
        $barcode = Stockbarcode::where('barcode', $code)->first();

        if (!$barcode) {
            return $this->sendErrorResponse('Product not found for this barcode', 404);
        }

        $stock = $barcode->stock;
dd($stock);
        return $this->showOne(
            new StockShowResource($stock)
        );
    }
}