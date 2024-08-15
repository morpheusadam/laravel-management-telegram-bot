<?php

namespace App\Providers;

use App\Queue\Connectors\CustomDatabaseConnector;
use Illuminate\Queue\QueueServiceProvider;

class CustomQueueServiceProvider extends QueueServiceProvider
{
    /**
     * Register the database queue connector.
     * @NOTE:-This will be called automatically. We override DatabaseConnector,registerRedisConnector method so that we can add custom code. Will add custom  as well
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerDatabaseConnector($manager)
    {
        $manager->addConnector('database', function () {
            return new CustomDatabaseConnector($this->app['db']);
        });
    }

}