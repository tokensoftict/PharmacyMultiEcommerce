<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponser
{
    /**
     * @param array|Collection $data
     * @param int $code
     * @return JsonResponse
     */
    private function successResponse(array|Collection|AnonymousResourceCollection $data, int $code) : JsonResponse
    {
        return response()->json($data, $code);
    }


    /**
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public final function errorResponse(string|array $message, int $code) : JsonResponse
    {
        return response()->json(['status' => false, 'error' => $message, 'code' => $code], $code);
    }


    /**
     * @param Collection $collection
     * @param int $code
     * @return JsonResponse
     */
    protected final function showAll(Collection|AnonymousResourceCollection $collection, int $code = 200) : JsonResponse
    {
        return $this->successResponse(['status' => true, 'data' => $collection],  $code);
    }


    /**
     * @param Model $model
     * @param int $code
     * @return JsonResponse
     */
    protected final function showOne(Model|JsonResource $model, int $code = 200) : JsonResponse
    {
        return $this->successResponse(['status' => true, 'data' => $model],  $code);
    }


    protected final function destroyResponse(bool $status) : JsonResponse
    {
        $data = [
            'message' => $status ? "Record has been deleted successfully" : "Error while deleting record",
            'code' =>  $status ? 200 : 500,
        ];

        return $status ? $this->successResponse(['status' => true, 'message'=>$data['message']], 200) : $this->errorResponse($data['message'], $data['code']);
    }
}
