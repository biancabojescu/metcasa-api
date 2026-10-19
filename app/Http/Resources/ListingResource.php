<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
            'deal_type' => $this->deal_type,
            'property_type' => $this->property_type,
            'rooms' => $this->rooms,
            'surface' => (float) $this->surface,
            'floor' => $this->floor,
            'price' => (float) $this->price,
            'images' => $this->mapImages($this->images ?? []),
            'is_featured' => (bool) $this->is_featured,
            'status' => $this->status,
            'archive_note' => $this->archive_note,
            'owner' => $this->whenLoaded('owner', fn () => [
                'id' => $this->owner?->id,
                'name' => $this->owner?->name,
                'email' => $this->owner?->email,
                'phone' => $this->owner?->phone,
                'avatar_url' => $this->owner?->avatar
                    ? Storage::disk('public')->url($this->owner->avatar)
                    : null,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @param  array<int, string>  $images
     * @return array<int, array{path: string, url: string}>
     */
    private function mapImages(array $images): array
    {
        return array_map(fn (string $path) => [
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ], $images);
    }
}
