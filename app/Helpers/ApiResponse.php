<?php

namespace App\Helpers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApiResponse
{
    public static function response($data, string $message, int $statusCode = Response::HTTP_OK, array $meta = [], ?string $status = null)
    {
        $status ??= $statusCode >= 400 ? 'error' : 'success';

        return response()->json([
            'meta' => array_merge(['code' => $statusCode,
                                    'status' => $status,
                                    'message' => $message,], $meta),
            'data' => $data,
        ], $statusCode);
    }
}