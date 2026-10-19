<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class FavoriteService
{
    /**
     * Returneaza doar anunturile active favoritate de user, paginate.
     */
    public function paginate(User $user, int $perPage = 9, int $page = 1): LengthAwarePaginator
    {
        return $user->favorites()
            ->where('listings.status', 'active')
            ->with('owner:id,name,email,phone,avatar')
            ->orderByPivot('created_at', 'desc')
            ->paginate(perPage: $perPage, page: $page);
    }

    public function add(User $user, Listing $listing): void
    {
        $user->favorites()->syncWithoutDetaching([$listing->id]);
    }

    public function remove(User $user, Listing $listing): void
    {
        $user->favorites()->detach($listing->id);
    }

    public function clear(User $user): void
    {
        $user->favorites()->detach();
    }

    /**
     * @return array<int, int> Lista de ID-uri ale anunturilor favoritate (active sau nu).
     *                         Folosit la boot pentru a hidrata UI-ul "inima rosie".
     */
    public function ids(User $user): array
    {
        return $user->favorites()->pluck('listings.id')->all();
    }
}
