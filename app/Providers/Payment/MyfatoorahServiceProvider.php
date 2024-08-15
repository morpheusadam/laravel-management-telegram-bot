<?php

namespace App\Providers\Payment;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\MyfatoorahServiceInterface;
use App\Services\Payment\MyfatoorahService;

class MyfatoorahServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
     
        $this->app->bind(MyfatoorahServiceInterface::class,MyfatoorahService::class);
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
