<?php

namespace App\Providers\Payment\Ecommerce;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\Ecommerce\StripeServiceEcommerceInterface;
use App\Services\Payment\Ecommerce\StripeServiceEcommerce;
class StripeServiceEcommerceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StripeServiceEcommerceInterface::class , StripeServiceEcommerce::class);
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
