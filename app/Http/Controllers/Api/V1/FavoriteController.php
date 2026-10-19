<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Http\Responses\ApiResponse;
use App\Models\Listing;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class FavoriteController extends Controller
{
    public function __construct(private FavoriteService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = max(1, min(50, (int) $request->query('per_page', 9)));
            $page = max(1, (int) $request->query('page', 1));

            $paginator = $this->service->paginate($request->user(), $perPage, $page);

            return ApiResponse::ok([
                'data' => ListingResource::collection($paginator->items())->toArray($request),
                'ids' => $this->service->ids($request->user()),
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

    public function store(Request $request, Listing $listing): JsonResponse
    {
        try {
            $this->service->add($request->user(), $listing);

            return ApiResponse::success('favorite.added', ['ids' => $this->service->ids($request->user())]);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function destroy(Request $request, Listing $listing): JsonResponse
    {
        try {
            $this->service->remove($request->user(), $listing);

            return ApiResponse::success('favorite.removed', ['ids' => $this->service->ids($request->user())]);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }

    public function clear(Request $request): JsonResponse
    {
        try {
            $this->service->clear($request->user());

            return ApiResponse::success('favorite.cleared', ['ids' => []]);
        } catch (Throwable $e) {
            return ApiResponse::serverError($e);
        }
    }
}
