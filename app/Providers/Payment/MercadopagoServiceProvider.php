<?php

namespace App\Providers\Payment;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\MercadopagoServiceInterface;
use App\Services\Payment\MercadopagoService;

class MercadopagoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
     
        $this->app->bind(MercadopagoServiceInterface::class,MercadopagoService::class);
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
