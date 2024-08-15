<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FlowbuilderServiceInterface;
use App\Services\FlowbuilderService;

class FlowbuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FlowbuilderServiceInterface::class , FlowbuilderService::class);
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
