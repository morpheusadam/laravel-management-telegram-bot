<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TelegramServiceInterface;
use App\Services\TelegramService;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->bind(TelegramServiceInterface::class , TelegramService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
