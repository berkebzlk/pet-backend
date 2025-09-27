<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Validation Exception
        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success'     => false,
                'error_code' => 'VALIDATION_ERROR',
                'message'    => 'Some fields are invalid.',
                'errors'     => $e->errors(), // field => [messages]
            ], \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        // Auth Exception
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json([
                'success'     => false,
                'error_code' => 'AUTH_ERROR',
                'message'    => 'Unauthenticated.',
            ], \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
        });

        // Forbidden (authorization)
        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success'     => false,
                'error_code' => 'FORBIDDEN',
                'message'    => 'You are not allowed to perform this action.',
            ], \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
        });

        // Model not found
        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success'     => false,
                'error_code' => 'NOT_FOUND',
                'message'    => 'The requested resource was not found.',
            ], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        });

        // HTTP errors (route not found, method not allowed, vs.)
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json([
                'success'     => false,
                'error_code' => 'HTTP_ERROR',
                'message'    => $e->getMessage() ?: 'HTTP Error occurred.',
            ], $e->getStatusCode());
        });

        // General / unexpected exceptions
        $exceptions->renderable(function (\Throwable $e) {
            $response = [
                'success'     => false,
                'error_code' => 'INTERNAL_ERROR',
                'message'    => 'An unexpected error occurred.',
            ];

            // Prod değilse detayları ekle
            if (!app()->isProduction()) {
                $response['exception'] = class_basename($e);
                $response['file']      = $e->getFile();
                $response['line']      = $e->getLine();
                $response['trace']     = $e->getTrace();
                $response['message']   = $e->getMessage(); // gerçek mesaj da gelsin
            }

            return response()->json($response, \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
