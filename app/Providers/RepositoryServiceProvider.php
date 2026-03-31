<?php

namespace App\Providers;

use App\Repositories\Interfaces\ShopInterface;
use App\Repositories\ShopRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ShopInterface::class,
            ShopRepository::class
        );
    }
}
