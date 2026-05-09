<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\ApiController;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderUploadController extends ApiController
{
    public function uploadProofOfPayment(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('proofs', $filename, 'public');

            $order->prove_of_payment = $path;
            $order->save();

            return $this->sendSuccessResponse([
                'message' => 'Proof of payment uploaded successfully',
                'path' => Storage::url($path),
            ]);
        }

        return $this->sendErrorResponse('No image uploaded', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }
}
