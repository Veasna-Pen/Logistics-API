<?php

use App\Helpers\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withProviders([
        App\Providers\AppServiceProvider::class,
    ])

    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (ValidationException $e, $request) {
            return ApiResponse::error(
                'Validation Error',
                422,
                $e->errors()
            );
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            return ApiResponse::error(
                'Resource not found',
                404
            );
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            return ApiResponse::error(
                'Unauthenticated',
                401
            );
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            return ApiResponse::error(
                'Forbidden',
                403
            );
        });

        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            return ApiResponse::error(
                $e->getMessage() ?: 'HTTP Error',
                $e->getStatusCode()
            );
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error(
                    config('app.debug') ? $e->getMessage() : 'Server Error',
                    500
                );
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            return ApiResponse::error(
                'Too many requests. Please try again later.',
                429
            );
        });
    })->create();
