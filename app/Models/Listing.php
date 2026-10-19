<?php

namespace App\Models;

use Database\Factories\ListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'title',
    'location',
    'description',
    'deal_type',
    'property_type',
    'rooms',
    'surface',
    'floor',
    'price',
    'images',
    'is_featured',
    'status',
    'archive_note',
])]
class Listing extends Model
{
    /** @use HasFactory<ListingFactory> */
    use HasFactory, SoftDeletes;

    public const DEAL_TYPES = ['Vanzare', 'Inchiriere'];

    public const PROPERTY_TYPES = ['Apartament', 'Casa', 'Teren', 'Comercial'];

    public const STATUSES = ['active', 'inactive'];

    protected function casts(): array
    {
        return [
            'rooms' => 'integer',
            'surface' => 'decimal:2',
            'price' => 'decimal:2',
            'images' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
