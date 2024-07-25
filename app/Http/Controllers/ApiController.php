<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as DBCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApiController extends Controller
{
    use ApiResponser;

    /**
     * @param array|Collection|AnonymousResourceCollection|DBCollection $data
     * @return JsonResponse
     */
    public final function sendSuccessResponse(array|Collection|AnonymousResourceCollection|DBCollection  $data) : JsonResponse
    {
        return $this->successResponse(['status' => true, 'data' => $data], ResponseAlias::HTTP_OK);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public final function sendSuccessMessageResponse(string $message) : JsonResponse
    {
        return $this->sendSuccessResponse(['message' => $message]);
    }

    public final function sendPaginatedSuccessResponse(array|Collection|AnonymousResourceCollection|DBCollection  $data) : JsonResponse
    {
        $meta = Arr::only($data['meta'], ['current_page', 'from', 'to', 'last_page', 'per_page',  'total']);
        $buttons = array_map(function($link){
            return [
                "label" => $link["label"],
                "active" => $link["active"],
                "parameters" => $link["url"] == null ? null : explode("?", $link["url"])[1]
            ];
        }, $data['meta']['links']);
        $meta['paginated_buttons'] = $buttons;
        $data = [
            'status' => true,
            'data' =>$data['data'],
            'meta'  => $meta,
        ];

        return $this->successResponse($data, ResponseAlias::HTTP_OK);
    }

    /**
     * @param string|array $message
     * @param int $code
     * @return JsonResponse
     */
    public final function sendErrorResponse(string|array $message, int $code) : JsonResponse
    {
        return $this->errorResponse($message, $code);
    }

}
