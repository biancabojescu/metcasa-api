<?php

namespace App\Http\Requests\Listing;

use App\Models\Listing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create listings') === true
            || $this->user()?->hasRole('super admin') === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'deal_type' => ['required', Rule::in(Listing::DEAL_TYPES)],
            'property_type' => ['required', Rule::in(Listing::PROPERTY_TYPES)],
            'rooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'surface' => ['required', 'numeric', 'min:1'],
            'floor' => ['nullable', 'string', 'max:16'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(Listing::STATUSES)],
            'archive_note' => ['nullable', 'string', 'max:1000'],
            'new_images' => ['nullable', 'array', 'max:10'],
            'new_images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
