<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PsgdcExceptionsHandler
{
    use ApiResponser;

    /**
     * @param $e
     * @param Request $request
     * @return JsonResponse|null
     */
    public static function handleExceptions($e, Request $request) : JsonResponse|null
    {
        if(\request()->is("api/*")) {
            return match (get_class($e)) {
                /*  This handle exception form invalid url link */
                NotFoundHttpException::class => (new self())->errorResponse("Record or Page not Found", ResponseAlias::HTTP_NOT_FOUND),
                /*  This handle exception model not found or Route binding Model not found */
                ModelNotFoundException::class => (new self())->errorResponse("Record not Found", ResponseAlias::HTTP_NOT_FOUND),
                /*  This handle exception for all validation in the system wrap them up and convert to json */
                ValidationException::class => (new self())->errorResponse($e->validator->errors()->getMessages(), ResponseAlias::HTTP_UNPROCESSABLE_ENTITY),
                QueryException::class => (new self())->errorResponse($e->getMessage(), ResponseAlias::HTTP_UNPROCESSABLE_ENTITY),
                /*  This handle login / authentication exception, trigger if the user has not login or token as expired  */
                AuthenticationException::class => (new self())->errorResponse("Unauthenticated. Please login to continue.", ResponseAlias::HTTP_UNAUTHORIZED),
                /* This error handle if there is an issue with CRM Database Interaction or maybe CRM Database is offline */
                /* invalid method */
                MethodNotAllowedHttpException::class => (new self())->errorResponse($e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR),
                /* Unknown error, we can report the error to slack or log it */
                default => self::reportError($e)
            };
        }
        report($e);
        return null;
    }

    /**
     * @param \Exception $e
     * @return JsonResponse
     */
    public static function reportError(\Exception $e) : JsonResponse|null
    {
        if(config("app.debug")){
            report($e);
        }else {
            return (new self())->errorResponse("An Unknown error occurred", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
        return null;
    }
}
