<?php

namespace App\Providers\Payment;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\MollieServiceInterface;
use App\Services\Payment\MollieService;

class MollieServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(MollieServiceInterface::class,MollieService::class);
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
