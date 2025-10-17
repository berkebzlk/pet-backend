<?php

use App\Modules\Core\Enums\HttpStatusEnum;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Modules\Auth\Exceptions\ExpiredAccessTokenException;

function createResponse($error_code, $message, $errors = [])
{
    return [
        'success'     => false,
        'error_code' => $error_code,
        'message' => $message,
        'errors' => $errors,
    ];
}

function addExceptionDetails($response, $e)
{
    $response['exception'] = class_basename($e);
    $response['file']      = $e->getFile();
    $response['line']      = $e->getLine();
    $response['trace']     = $e->getTrace();
    $response['message']   = $e->getMessage();

    return $response;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register API middleware to pre-detect expired tokens (run before auth:api)
        $middleware->prependToGroup('api', [\App\Modules\Auth\Middleware\CheckExpiredAccessToken::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Validation Exception
        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e) {
            $response = createResponse('VALIDATION_ERROR', __('validation.invalid'), $e->errors());

            if (!app()->isProduction()) {
                $response = addExceptionDetails($response, $e);
            }

            return response()->json($response, \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        // Auth Exception
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e) {
            $response = createResponse('AUTH_ERROR', __('http.' . HttpStatusEnum::UNAUTHORIZED->value));

            if (!app()->isProduction()) {
                $response = addExceptionDetails($response, $e);
            }

            return response()->json($response, \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
        });

        // Custom expired token response (401 with specific error_code)
        $exceptions->renderable(function (ExpiredAccessTokenException $e) {
            $response = createResponse('TOKEN_EXPIRED', __('http.' . HttpStatusEnum::UNAUTHORIZED->value));

            if (!app()->isProduction()) {
                $response = addExceptionDetails($response, $e);
            }

            return response()->json($response, \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
        });

        // Forbidden (authorization)
        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e) {
            $response = createResponse('FORBIDDEN', __('http.' . HttpStatusEnum::FORBIDDEN->value));

            if (!app()->isProduction()) {
                $response = addExceptionDetails($response, $e);
            }

            return response()->json($response, \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
        });

        // Model not found
        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response = createResponse('NOT_FOUND', __('http.' . HttpStatusEnum::NOT_FOUND->value));

            if (!app()->isProduction()) {
                $response = addExceptionDetails($response, $e);
            }

            return response()->json($response, \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        });

        // HTTP errors - farklı exception türleri için
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            $response = createResponse('NOT_FOUND', __('http.' . HttpStatusEnum::NOT_FOUND->value));

            if (!app()->isProduction()) {
                $response = addExceptionDetails($response, $e);
            }

            return response()->json($response, HttpStatusEnum::NOT_FOUND->value);
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
            $response = createResponse('METHOD_NOT_ALLOWED', __('http.' . HttpStatusEnum::METHOD_NOT_ALLOWED->value));

            if (!app()->isProduction()) {
                $response = addExceptionDetails($response, $e);
            }

            return response()->json($response, HttpStatusEnum::METHOD_NOT_ALLOWED->value);
        });

        // General / unexpected exceptions
        $exceptions->renderable(function (\Throwable $e) {
            $response = createResponse('INTERNAL_ERROR', __('http.' . HttpStatusEnum::INTERNAL_SERVER_ERROR->value));

            if (!app()->isProduction()) {
                $response = addExceptionDetails($response, $e);
            }

            return response()->json($response, \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
