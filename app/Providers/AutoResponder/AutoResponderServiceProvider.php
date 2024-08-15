<?php

namespace App\Providers\AutoResponder;

use Illuminate\Support\ServiceProvider;
use App\Services\AutoResponder\AutoResponderService;
use App\Services\AutoResponder\AutoResponderServiceInterface;

class AutoResponderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(AutoResponderServiceInterface::class,AutoResponderService::class);
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
