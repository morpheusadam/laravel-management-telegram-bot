<?php

namespace App\Providers\Payment;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\RazorpayServiceInterface;
use App\Services\Payment\RazorpayService;

class RazorpayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(RazorpayServiceInterface::class,RazorpayService::class);
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
