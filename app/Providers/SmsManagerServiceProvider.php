<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Services\SmsManagerService;
use App\Services\SmsManagerServiceInterface;



class SmsManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SmsManagerServiceInterface::class,SmsManagerService::class);
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
