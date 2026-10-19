<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Listing>
 */
class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        $propertyType = fake()->randomElement(Listing::PROPERTY_TYPES);
        $dealType = fake()->randomElement(Listing::DEAL_TYPES);

        $hasRooms = in_array($propertyType, ['Apartament', 'Casa'], true);
        $rooms = $hasRooms ? fake()->numberBetween(1, 5) : null;

        $surface = match ($propertyType) {
            'Teren' => fake()->numberBetween(300, 2000),
            'Casa' => fake()->numberBetween(80, 400),
            'Comercial' => fake()->numberBetween(40, 250),
            default => fake()->numberBetween(28, 130),
        };

        $price = $dealType === 'Vanzare'
            ? match ($propertyType) {
                'Teren' => fake()->numberBetween(20_000, 500_000),
                'Casa' => fake()->numberBetween(120_000, 850_000),
                'Comercial' => fake()->numberBetween(80_000, 1_200_000),
                default => fake()->numberBetween(45_000, 220_000),
            }
            : fake()->numberBetween(200, 2500);

        return [
            'user_id' => User::factory(),
            'title' => $this->buildTitle($propertyType, $rooms),
            'location' => 'Iasi, '.fake()->randomElement([
                'Centru', 'Copou', 'Tatarasi', 'Galata', 'Pacurari', 'Nicolina',
                'Bularga', 'Alexandru cel Bun', 'Dacia', 'Tudor Vladimirescu',
                'Mircea cel Batran', 'Canta', 'Podu Ros', 'Pacurari',
            ]),
            'description' => fake()->paragraphs(2, true),
            'deal_type' => $dealType,
            'property_type' => $propertyType,
            'rooms' => $rooms,
            'surface' => $surface,
            'floor' => $hasRooms && $propertyType === 'Apartament'
                ? fake()->numberBetween(0, 10).'/'.fake()->numberBetween(4, 12)
                : null,
            'price' => $price,
            'images' => [],
            'is_featured' => fake()->boolean(15),
            'status' => 'active',
        ];
    }

    private function buildTitle(string $propertyType, ?int $rooms): string
    {
        return match ($propertyType) {
            'Apartament' => $rooms === 1 ? 'Garsoniera moderna' : 'Apartament '.$rooms.' camere, '.fake()->randomElement(['decomandat', 'semi-decomandat', 'mobilat', 'renovat']),
            'Casa' => 'Casa '.$rooms.' camere'.fake()->randomElement([' cu gradina', ' individuala', ' tip duplex', '']),
            'Teren' => 'Teren '.fake()->randomElement(['intravilan', 'extravilan']).', '.fake()->numberBetween(300, 2000).' mp',
            'Comercial' => 'Spatiu comercial '.fake()->randomElement(['parter stradal', 'open space', 'depozit', 'birou']),
            default => 'Proprietate',
        };
    }
}
