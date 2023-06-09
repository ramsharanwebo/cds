<?php

namespace App\Providers;

use App\Interfaces\CDSUserInterface;
use App\Interfaces\CustomerInterface;
use App\Interfaces\DucketInterface;
use App\Interfaces\LocationInterface;
use App\Interfaces\PermissionInterface;
use App\Interfaces\RoleInterface;
use App\Repositories\AttachPermissionRepository;
use App\Repositories\CDSUserRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DucketRepository;
use App\Repositories\LocationRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
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
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(CDSUserInterface::class, CDSUserRepository::class);
        $this->app->bind(AttachPermissionInterface::class, AttachPermissionRepository::class);
        $this->app->bind(CustomerInterface::class, CustomerRepository::class);
        $this->app->bind(LocationInterface::class, LocationRepository::class);
        $this->app->bind(DucketInterface::class, DucketRepository::class);
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
