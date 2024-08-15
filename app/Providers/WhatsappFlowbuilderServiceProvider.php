<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WhatsappFlowbuilderServiceInterface;
use App\Services\WhatsappFlowbuilderService;

class WhatsappFlowbuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WhatsappFlowbuilderServiceInterface::class , WhatsappFlowbuilderService::class);
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
