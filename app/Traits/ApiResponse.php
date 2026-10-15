<?php

namespace App\Traits;

use Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;    

trait ApiResponse
{
    public function successResponse( $data =null, string $message = '', int $status = 200 ): JsonResponse {

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status' => $status
        ], $status);
    }

    public function errorResponse(string $message, int $status = 400, $errors = null): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'status' => $status
        ], $status);
    }
}
