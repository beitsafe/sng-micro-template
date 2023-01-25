<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ErrorResponse
{
    /**
     * Data Response
     * @param $data
     * @return JsonResponse
     */
    public function dataResponse($data): JsonResponse
    {
        return response()->json(['content' => $data], Response::HTTP_OK);
    }

    /**
     * Error Response
     * @param string $message
     * @param int $code
     * @return JsonResponse
     *
     */
    public function errorResponse(mixed $message, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
}
