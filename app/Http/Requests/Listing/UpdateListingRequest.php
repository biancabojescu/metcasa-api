<?php

namespace App\Http\Requests\Listing;

use App\Models\Listing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        /** @var Listing|null $listing */
        $listing = $this->route('listing');
        if (! $listing) {
            return false;
        }

        if ($user->hasRole('super admin') || $user->can('edit any listing')) {
            return true;
        }

        return $listing->user_id === $user->id && $user->can('edit own listings');
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'deal_type' => ['sometimes', 'required', Rule::in(Listing::DEAL_TYPES)],
            'property_type' => ['sometimes', 'required', Rule::in(Listing::PROPERTY_TYPES)],
            'rooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'surface' => ['sometimes', 'required', 'numeric', 'min:1'],
            'floor' => ['nullable', 'string', 'max:16'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(Listing::STATUSES)],
            'archive_note' => ['nullable', 'string', 'max:1000'],
            'keep_images' => ['nullable', 'array'],
            'keep_images.*' => ['string'],
            'new_images' => ['nullable', 'array', 'max:10'],
            'new_images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
