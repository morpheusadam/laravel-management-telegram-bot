<?php

namespace App\Providers\Payment\Ecommerce;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\Ecommerce\PaypalServiceEcommerce;
use App\Services\Payment\Ecommerce\PaypalServiceEcommerceInterface;
class PaypalServiceEcommerceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaypalServiceEcommerceInterface::class,PaypalServiceEcommerce::class);
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
