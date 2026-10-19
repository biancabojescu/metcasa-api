<?php

namespace App\Repositories;

use App\Models\Listing;
use App\Repositories\Contracts\ListingRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ListingRepository extends BaseRepository implements ListingRepositoryInterface
{
    public function __construct(Listing $model)
    {
        parent::__construct($model);
    }

    public static function modelClass(): string
    {
        return Listing::class;
    }

    public function filtered(array $filters): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) ($filters['per_page'] ?? 12)));

        $query = $this->query()->with('owner:id,name,email,phone,avatar');

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('location', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if (! empty($filters['location'])) {
            $query->where('location', 'like', '%'.$filters['location'].'%');
        }

        if (! empty($filters['deal_type'])) {
            $query->where('deal_type', $filters['deal_type']);
        }

        if (! empty($filters['property_type'])) {
            $query->where('property_type', $filters['property_type']);
        }

        if (isset($filters['rooms']) && $filters['rooms'] !== '' && $filters['rooms'] !== null) {
            if ($filters['rooms'] === '4+' || (int) $filters['rooms'] >= 4) {
                $query->where('rooms', '>=', 4);
            } else {
                $query->where('rooms', (int) $filters['rooms']);
            }
        }

        if (! empty($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (! empty($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (! empty($filters['surface_min'])) {
            $query->where('surface', '>=', $filters['surface_min']);
        }

        if (! empty($filters['surface_max'])) {
            $query->where('surface', '<=', $filters['surface_max']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            // Default doar active in listari publice; admin poate cere explicit
            if (empty($filters['include_all_statuses'])) {
                $query->where('status', 'active');
            }
        }

        if (! empty($filters['owner_id'])) {
            $query->where('user_id', $filters['owner_id']);
        }

        if (! empty($filters['ids']) && is_array($filters['ids'])) {
            $query->whereIn('id', array_filter($filters['ids'], 'is_numeric'));
        }

        $sort = $filters['sort'] ?? 'recent';
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'surface_desc' => $query->orderByDesc('surface'),
            default => $query->orderByDesc('created_at'),
        };

        return $query;
    }
}
