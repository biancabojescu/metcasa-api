<?php

namespace App\Providers;

use App\Repositories\Contracts\ListingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\ListingRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        ListingRepositoryInterface::class => ListingRepository::class,
    ];

    public function register(): void {}

    public function boot(): void {}
}
