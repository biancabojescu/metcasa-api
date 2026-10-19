<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use App\Repositories\Contracts\ListingRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ListingService
{
    public const IMAGE_DISK = 'public';

    public const IMAGE_DIR = 'listings';

    public function __construct(private ListingRepositoryInterface $listings) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->listings->filtered($filters);
    }

    public function find(int $id): Listing
    {
        /** @var Listing $listing */
        $listing = $this->listings->query()->with('owner:id,name,email,phone,avatar')->findOrFail($id);

        return $listing;
    }

    public function create(User $owner, array $data, array $uploadedImages = []): Listing
    {
        $imagesPaths = $this->storeImages($uploadedImages);

        /** @var Listing $listing */
        $listing = $this->listings->create(array_merge(
            $this->scrubAttributes($data),
            [
                'user_id' => $owner->id,
                'images' => $imagesPaths,
            ]
        ));

        return $listing->fresh('owner');
    }

    public function update(Listing $listing, array $data, array $keepImages = [], array $newImages = []): Listing
    {
        $existing = $listing->images ?? [];

        $kept = array_values(array_intersect($existing, $keepImages));
        $removed = array_values(array_diff($existing, $kept));
        $this->deleteImageFiles($removed);

        $added = $this->storeImages($newImages);

        $attributes = $this->scrubAttributes($data);
        $attributes['images'] = array_values(array_merge($kept, $added));

        return $this->listings->update($listing, $attributes)->load('owner');
    }

    public function delete(Listing $listing): void
    {
        $this->deleteImageFiles($listing->images ?? []);
        $this->listings->delete($listing);
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @return array<int, string>
     */
    private function storeImages(array $files): array
    {
        $paths = [];
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $path = $file->store(self::IMAGE_DIR, self::IMAGE_DISK);
            if ($path) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function deleteImageFiles(array $paths): void
    {
        if (empty($paths)) {
            return;
        }
        Storage::disk(self::IMAGE_DISK)->delete($paths);
    }

    private function scrubAttributes(array $data): array
    {
        // Convertim string-uri vide la null pentru floor/rooms
        if (array_key_exists('floor', $data) && $data['floor'] === '') {
            $data['floor'] = null;
        }
        if (array_key_exists('rooms', $data) && $data['rooms'] === '') {
            $data['rooms'] = null;
        }
        if (array_key_exists('description', $data) && $data['description'] === '') {
            $data['description'] = null;
        }

        return $data;
    }
}
