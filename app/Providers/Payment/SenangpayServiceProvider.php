<?php

namespace App\Providers\Payment;
use Illuminate\Support\ServiceProvider;
use App\Services\Payment\SenangpayServiceInterface;
use App\Services\Payment\SenangpayService;
class SenangpayServiceProvider extends ServiceProvider  
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SenangpayServiceInterface::class,SenangpayService::class);
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
