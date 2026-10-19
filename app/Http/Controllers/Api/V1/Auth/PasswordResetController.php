<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Exceptions\Api\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PasswordResetController extends Controller
{
    public function __construct(private PasswordResetService $service) {}

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $key = $this->service->sendResetLink((string) $request->string('email'));

            return ApiResponse::success($key);
        } catch (ApiException $e) {
            return ApiResponse::error($e->messageKey, $e->status);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $key = $this->service->reset(
                (string) $request->string('email'),
                (string) $request->string('token'),
                (string) $request->string('password'),
            );

            return ApiResponse::success($key);
        } catch (ApiException $e) {
            return ApiResponse::error($e->messageKey, $e->status);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }
}
