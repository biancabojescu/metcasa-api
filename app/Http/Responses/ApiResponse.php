<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Helper centralizat pentru construit raspunsuri API.
 * Mesajele vin din lang/{locale}/responses.php prin cheia "section.key".
 */
class ApiResponse
{
    /**
     * Raspuns de succes cu payload arbitrar.
     */
    public static function ok(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $status);
    }

    /**
     * Raspuns de succes cu un mesaj din responses.php.
     */
    public static function success(string $messageKey, array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(array_merge([
            'message' => __('responses.'.$messageKey),
        ], $data), $status);
    }

    /**
     * Raspuns de eroare cu un mesaj din responses.php.
     */
    public static function error(string $messageKey, int $status = Response::HTTP_BAD_REQUEST, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'message' => __('responses.'.$messageKey),
        ], $extra), $status);
    }

    /**
     * Eroare cu mesaj brut (cand nu vine din lang) - folosit pt mesaje deja localizate
     * provenind din exceptii cu mesaj propriu.
     */
    public static function raw(string $message, int $status = Response::HTTP_BAD_REQUEST, array $extra = []): JsonResponse
    {
        return response()->json(array_merge(['message' => $message], $extra), $status);
    }

    /**
     * Fallback pentru exceptii neasteptate. Logheaza eroarea si raspunde generic.
     */
    public static function serverError(Throwable $e, string $fallbackKey = 'common.server_error'): JsonResponse
    {
        Log::error($e->getMessage(), ['exception' => $e]);

        return self::error($fallbackKey, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
