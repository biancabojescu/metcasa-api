<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->auth->register(
                $request->validated(),
                (string) $request->input('device_name', 'web'),
                (bool) $request->boolean('remember', true),
            );

            return ApiResponse::ok([
                'message' => __('responses.auth.registered'),
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'expires_at' => $result['expires_at'],
            ], Response::HTTP_CREATED);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->auth->login(
                (string) $request->string('email'),
                (string) $request->string('password'),
                (string) $request->input('device_name', 'web'),
                (bool) $request->boolean('remember'),
            );

            return ApiResponse::ok([
                'message' => __('responses.auth.logged_in'),
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'expires_at' => $result['expires_at'],
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->auth->logoutCurrentToken($request->user());

            return ApiResponse::success('auth.logged_out');
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            return ApiResponse::ok([
                'user' => new UserResource($request->user()),
            ]);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }
}
