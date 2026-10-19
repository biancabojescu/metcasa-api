<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Exceptions\Api\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EmailVerificationController extends Controller
{
    public function __construct(private EmailVerificationService $service) {}

    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        try {
            if (! $request->hasValidSignature()) {
                return ApiResponse::error('email_verification.expired_link', Response::HTTP_FORBIDDEN);
            }

            $payload = $this->service->verify($id, $hash);

            return ApiResponse::ok([
                'message' => __('responses.'.$payload['message_key']),
                'already_verified' => $payload['already_verified'],
            ]);
        } catch (ApiException $e) {
            return ApiResponse::error($e->messageKey, $e->status);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function resend(Request $request): JsonResponse
    {
        try {
            $key = $this->service->resendFor($request->user());

            return ApiResponse::success($key);
        } catch (ApiException $e) {
            return ApiResponse::error($e->messageKey, $e->status);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }
}
