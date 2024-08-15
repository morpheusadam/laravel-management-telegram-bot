<?php

namespace App\Providers\Payment;
use Illuminate\Support\ServiceProvider;
use App\Services\Payment\YoomoneyServiceInterface;
use App\Services\Payment\YoomoneyService;
class YoomoneyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(YoomoneyServiceInterface::class,YoomoneyService::class);
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
