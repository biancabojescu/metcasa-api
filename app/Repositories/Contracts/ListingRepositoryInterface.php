<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface ListingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function filtered(array $filters): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function applyFilters(Builder $query, array $filters): Builder;
}
