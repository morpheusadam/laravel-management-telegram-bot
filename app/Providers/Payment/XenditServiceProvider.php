<?php

namespace App\Providers\Payment;
use Illuminate\Support\ServiceProvider;
use App\Services\Payment\XenditServiceInterface;
use App\Services\Payment\XenditService;
class XenditServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(XenditServiceInterface::class,XenditService::class);
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
