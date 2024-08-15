<?php

namespace App\Providers\Payment;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\ToyyibpayServiceInterface;
use App\Services\Payment\ToyyibpayService;


class ToyyibpayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ToyyibpayServiceInterface::class , ToyyibpayService::class);
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
