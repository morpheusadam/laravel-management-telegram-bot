<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WhatsappServiceInterface;
use App\Services\WhatsappService;

class WhatsappServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WhatsappServiceInterface::class,WhatsappService::class);
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
