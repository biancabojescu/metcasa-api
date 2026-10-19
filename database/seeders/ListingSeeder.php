<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'daniel.bojescu@gmail.com')->first();
        if (! $owner) {
            $this->command->warn('SuperAdmin user not found, skipping listings seed.');

            return;
        }

        Listing::factory()
            ->count(18)
            ->state(['user_id' => $owner->id])
            ->create();
    }
}
