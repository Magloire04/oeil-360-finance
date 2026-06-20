<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, ?array $meta = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'data'  => $data,
            'meta'  => $meta,
            'error' => null,
        ], $status, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public static function error(
        string $message,
        ?string $code = null,
        mixed $details = null,
        int $status = 400
    ): JsonResponse {
        return response()->json([
            'data'  => null,
            'meta'  => null,
            'error' => [
                'message' => $message,
                'code'    => $code,
                'details' => $details,
            ],
        ], $status, [], JSON_PRESERVE_ZERO_FRACTION);
    }
}
