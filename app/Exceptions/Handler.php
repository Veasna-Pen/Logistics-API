<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Helpers\ApiResponse;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return ApiResponse::error(
                'Validation Error',
                422,
                $e->errors()
            );
        }

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ApiResponse::error(
                'Resource not found',
                404
            );
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return ApiResponse::error(
                'Unauthenticated',
                401
            );
        }

        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return ApiResponse::error(
                'Forbidden',
                403
            );
        }

        if ($e instanceof HttpExceptionInterface) {
            return ApiResponse::error(
                $e->getMessage() ?: 'Error',
                $e->getStatusCode()
            );
        }

        if ($request->expectsJson()) {
            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : 'Server Error',
                500
            );
        }

        return parent::render($request, $e);
    }
}
