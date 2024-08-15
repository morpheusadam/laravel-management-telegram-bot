<?php

namespace App\Providers\Payment;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\StripeServiceInterface;
use App\Services\Payment\StripeService;
class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StripeServiceInterface::class , StripeService::class);
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
