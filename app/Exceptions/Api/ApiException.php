<?php

namespace App\Exceptions\Api;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exceptie de domeniu cu cheie de mesaj din lang/{locale}/responses.php si HTTP status.
 * Aruncata de service-uri, prinsa in controlere si convertita in JSON cu ApiResponse::error.
 */
class ApiException extends RuntimeException
{
    public function __construct(
        public readonly string $messageKey,
        public readonly int $status = Response::HTTP_BAD_REQUEST,
    ) {
        parent::__construct($messageKey, $status);
    }
}
