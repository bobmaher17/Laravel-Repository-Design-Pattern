<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Articles\ArticlesRepositoryInterface;
use App\Repositories\Articles\ArticlesRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class RepositoryServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ArticlesRepositoryInterface::class, ArticlesRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
