<?php

namespace App\Providers\Payment;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\FlutterwaveServiceInterface;
use App\Services\Payment\FlutterwaveService;

class FlutterwaveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FlutterwaveServiceInterface::class,FlutterwaveService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
