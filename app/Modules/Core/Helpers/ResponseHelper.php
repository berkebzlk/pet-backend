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
}
