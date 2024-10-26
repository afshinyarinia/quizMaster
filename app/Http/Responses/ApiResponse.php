<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Create a success response.
     *
     * @param mixed|null $data
     * @param string|null $message
     * @param int $status
     * @return JsonResponse
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Create an error response.
     *
     * @param string $message
     * @param int $status
     * @param \Exception|null $exception
     * @return JsonResponse
     */
    public static function error(
        string $message,
        int $status = 400,
        ?\Exception $exception = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (config('app.debug') && $exception) {
            $response['debug'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        return response()->json($response, $status);
    }
}
