<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\Api\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Http\Requests\Listing\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Http\Responses\ApiResponse;
use App\Models\Listing;
use App\Services\ListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ListingController extends Controller
{
    public function __construct(private ListingService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $paginator = $this->service->list($request->query());

            return ApiResponse::ok([
                'data' => ListingResource::collection($paginator->items())->toArray($request),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
            ]);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $listing = $this->service->find($id);

            return ApiResponse::ok([
                'data' => (new ListingResource($listing))->resolve(),
            ]);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function store(StoreListingRequest $request): JsonResponse
    {
        try {
            $listing = $this->service->create(
                $request->user(),
                $request->validated(),
                $request->file('new_images') ?? [],
            );

            return ApiResponse::ok([
                'message' => __('responses.listing.created'),
                'data' => (new ListingResource($listing))->resolve(),
            ], Response::HTTP_CREATED);
        } catch (ApiException $e) {
            return ApiResponse::error($e->messageKey, $e->status);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function update(UpdateListingRequest $request, Listing $listing): JsonResponse
    {
        try {
            $keep = $request->input('keep_images', []);
            $new = $request->file('new_images') ?? [];

            $updated = $this->service->update($listing, $request->validated(), $keep, $new);

            return ApiResponse::ok([
                'message' => __('responses.listing.updated'),
                'data' => (new ListingResource($updated))->resolve(),
            ]);
        } catch (ApiException $e) {
            return ApiResponse::error($e->messageKey, $e->status);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function destroy(Request $request, Listing $listing): JsonResponse
    {
        try {
            $user = $request->user();
            $canAny = $user?->hasRole('super admin') || $user?->can('delete any listing');
            $canOwn = $listing->user_id === $user?->id && $user?->can('delete own listings');

            if (! $canAny && ! $canOwn) {
                return ApiResponse::error('common.forbidden', Response::HTTP_FORBIDDEN);
            }

            $this->service->delete($listing);

            return ApiResponse::success('listing.deleted');
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }
}
