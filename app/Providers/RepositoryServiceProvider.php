<?php

namespace App\Providers;


use App\Interfaces\ArchRepositoryInterface;
use App\Interfaces\ChasisRepositoryInterface;
use App\Interfaces\DataRepositoryInterface;
use App\Interfaces\EloquentRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ScanRepositoryInterface;
use App\Interfaces\TyreRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\ArchRepository;
use App\Repositories\ChasisRepository;
use App\Repositories\DataRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ScanRepository;
use App\Repositories\TyreRepository;
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
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(OptionRepositoryInterface::class, OptionRepository::class);
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
