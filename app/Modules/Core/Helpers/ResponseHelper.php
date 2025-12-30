<?php

namespace App\Modules\Core\Helpers;

use App\Modules\Core\Enums\HttpStatusEnum;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success(
        mixed $data = null,
        int $statusCode = HttpStatusEnum::OK->value,
        ?string $message = null
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }
    public static function error(
        string $message,
        int $statusCode = HttpStatusEnum::BAD_REQUEST->value,
        mixed $data = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }
}
