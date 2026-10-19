<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Throwable;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $service) {}

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $this->service->update(
                $request->user(),
                $request->validated(),
                $request->file('avatar'),
                $request->boolean('remove_avatar'),
            );

            return ApiResponse::ok([
                'message' => __('responses.profile.updated'),
                'user' => (new UserResource($user))->resolve(),
            ]);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }
}
